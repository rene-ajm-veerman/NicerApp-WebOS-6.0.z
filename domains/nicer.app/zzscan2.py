import json, urllib.request, base64, re, sys, time

P = "chd8FJEUDndjerJQWNERIsdDkojweNFENSjeiowljfkNENRKWlRPEK"
A = "Basic " + base64.b64encode(("admin:" + P).encode()).decode()

def get(u, timeout=120):
    r = urllib.request.Request(u)
    r.add_header("Authorization", A)
    return urllib.request.urlopen(r, timeout=timeout).read()

keypat = re.compile(r'(cc_?num|credit.?card|card_?num|\bcvv\b|\bcvc\b|\bcvv2\b|\bpan\b|expir|billing|payment|order|subscri|checkout|iban|cardholder)', re.I)
pan_re = re.compile(r'(?<![0-9])([0-9]{13,19})(?![0-9])')

def luhn(s):
    try:
        n = 0
        for i, ch in enumerate(reversed(s)):
            d = ord(ch) - 48
            if i % 2 == 1:
                d *= 2
                if d > 9:
                    d -= 9
            n += d
        return n % 10 == 0
    except Exception:
        return False

def walk(obj, path, out_keys, out_pans):
    if isinstance(obj, dict):
        for k, v in obj.items():
            kl = str(k)
            if keypat.search(kl):
                out_keys[kl] = out_keys.get(kl, 0) + 1
            walk(v, path + "." + kl, out_keys, out_pans)
    elif isinstance(obj, list):
        for i, v in enumerate(obj):
            walk(v, path + "[]", out_keys, out_pans)
    elif isinstance(obj, str):
        for m in pan_re.finditer(obj):
            s = m.group(1)
            out_pans.append((s, path))

dbs = [d for d in json.loads(get("http://127.0.0.1:5984/_all_dbs")) if not d.startswith("_")]
report = {}
for db in dbs:
    try:
        info = json.loads(get("http://127.0.0.1:5984/" + db, 60))
        dc = info.get("doc_count", 0)
    except Exception as e:
        report[db] = {"error": str(e)}
        continue
    if dc == 0:
        continue
    cap = 5000 if dc > 5000 else dc
    keys_hit = {}
    pans = []
    scanned = 0
    startkey = None
    try:
        while scanned < cap:
            lim = min(2000, cap - scanned)
            u = "http://127.0.0.1:5984/%s/_all_docs?include_docs=true&limit=%d" % (db, lim)
            if startkey:
                import urllib.parse
                u += "&startkey=" + urllib.parse.quote(json.dumps(startkey))
            d = json.loads(get(u, 180))
            rows = d.get("rows", [])
            if not rows:
                break
            for row in rows:
                doc = row.get("doc")
                if doc:
                    walk(doc, row.get("id", ""), keys_hit, pans)
                    scanned += 1
            startkey = rows[-1].get("id") + "\u0000"
            if len(rows) < lim:
                break
    except Exception as e:
        report.setdefault(db, {})["scan_error"] = str(e)
    uniq_valid = {}
    for s, p in pans:
        if luhn(s):
            uniq_valid[s] = uniq_valid.get(s, 0) + 1
    report.setdefault(db, {}).update({
        "doc_count": dc,
        "scanned": scanned,
        "key_hits": dict(sorted(keys_hit.items(), key=lambda x: -x[1])[:15]),
        "pan_like": len(pans),
        "pan_luhn_unique": len(uniq_valid),
        "pan_samples": [{"pan": s, "path": p} for s, p in pans[:5]],
        "luhn_samples": [s for s in list(uniq_valid)[:10]],
    })

open("/var/www/NicerAppWebOS-v6.y.z/domains/nicer.app/zz_couch_scan.json", "w").write(json.dumps(report, indent=1))
for db, r in report.items():
    if r.get("key_hits") or r.get("pan_like") or r.get("scan_error") or r.get("error"):
        print(db, "docs=%s scanned=%s panlike=%s luhn=%s keys=%s" % (
            r.get("doc_count"), r.get("scanned"), r.get("pan_like"), r.get("pan_luhn_unique"),
            list(r.get("key_hits", {}).keys())[:8]))
print("SCAN_DONE dbs=%d" % len(report))

