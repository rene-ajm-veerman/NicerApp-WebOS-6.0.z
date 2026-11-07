import json, urllib.request, base64
P="chd8FJEUDndjerJQWNERIsdDkojweNFENSjeiowljfkNENRKWlRPEK"
A="Basic "+base64.b64encode(("admin:"+P).encode()).decode()
def get(u):
    r=urllib.request.Request(u); r.add_header("Authorization",A)
    return urllib.request.urlopen(r,timeout=60).read()
dbs=json.loads(get("http://127.0.0.1:5984/_all_dbs"))
print("DBCOUNT",len(dbs))
tot=0
for db in dbs:
    try:
        i=json.loads(get("http://127.0.0.1:5984/"+db))
        dc=i.get("doc_count",0); sz=i.get("sizes",{}).get("active",0)
        tot+=dc
        print("%-60s docs=%-6s active=%s" % (db,dc,sz))
    except Exception as e:
        print(db,"ERR",e)
print("TOTALDOCS",tot)

