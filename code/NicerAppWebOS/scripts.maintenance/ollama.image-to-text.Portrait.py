#!/var/www/nicer.app-5.10.z/code/NicerAppWebOS/scripts.maintenance/venv-ollama/bin/python
# (C) 2026 Rene AJM Veerman and https://grok.com
# MIT licensed.
import ollama
import os
import json
import time
import fcntl
import subprocess
from pathlib import Path

import fcntl
import sys
import time

LOCK_FILE = "ollama-image-to-text.lock"   # or in your script dir

def acquire_lock():
    lock_fd = open(LOCK_FILE, 'w')
    try:
        fcntl.flock(lock_fd, fcntl.LOCK_EX | fcntl.LOCK_NB)
        return lock_fd
    except BlockingIOError:
        print("Another instance holds the lock. Exiting.")
        sys.exit(1)

lock_file = acquire_lock()   # put this right at the top after imports

# === Configuration - change these if needed ===
MODELS = [
    'llava-llama3',   # Balanced, detailed
    'llava:7b',       # Concise captions
    'bakllava',       # Creative/mood-focused
    'moondream'       # Keyword-heavy for SEO
]

# Define multiple prompts — each gets its own key in the output
PROMPTS = [
    {
        "name": "detailed",
        "text": (
            "Describe this wallpaper in detail for alt text and SEO. "
            "Include main subject, style, mood, colors, lighting, key elements. "
            "Keep it under 120 words. Write in natural, flowing sentences."
        )
    },
    {
        "name": "keywords",
        "text": (
            "Generate a list of 10–20 precise keywords and tags for this wallpaper. "
            "Include: main objects, location/setting, style (e.g. cyberpunk, minimalist), "
            "mood/emotion, colors, lighting/time of day, artistic influences. "
            "Output only as comma-separated list of the actual keywords — no full sentences."
        )
    }
    # You can easily add more prompts here later
]

BASE_DIR = Path('/var/www/nicer.app-5.10.z/domains/nicer.app/siteMedia/backgrounds/Portrait')

INPUTS = [
    'wallpaper_descriptions_multi_prompts-Portrait..b1.json',
    'wallpaper_descriptions_multi_prompts-Portrait..b2.json'
]

OUTPUT_FILE = 'wallpaper_descriptions_multi_prompts-Portrait.json'

CHECKPOINT_INTERVAL = 50  # Save every X images

# === Main code ===

def should_skip_folder(folder_name: str) -> bool:
    return folder_name.lower().startswith('thumbs')

def describe_image_with_model_and_prompt(image_path: Path, model: str, prompt_text: str) -> str:
    try:
        response = ollama.chat(
            model=model,
            messages=[
                {
                    'role': 'user',
                    'content': prompt_text,
                    'images': [str(image_path)]
                }
            ],
            options={'num_ctx': 2048}
        )
        return response['message']['content'].strip()
    except Exception as e:
        return f"ERROR: {str(e)}"

# ── Collect images (same as before) ──
print(f"Scanning folders under: {BASE_DIR}")
image_extensions = {'.jpg', '.jpeg', '.png', '.webp', '.gif'}

image_files = []
for root, dirs, files in os.walk(BASE_DIR):
    dirs[:] = [d for d in dirs if not should_skip_folder(d)]
    for file in files:
        if Path(file).suffix.lower() in image_extensions:
            image_files.append(Path(root) / file)

print(f"Found {len(image_files)} total images")

RESULTS_DIR = Path("./")   # e.g. "/home/user/nicerApp/NicerAppWebOS/data/wallpapers"
MAIN_FILE = RESULTS_DIR / "wallpaper_descriptions_multi_prompts-Portrait.json"
BATCH_FILES = [
    RESULTS_DIR / "wallpaper_descriptions_multi_prompts-Portrait..b1.json",
    RESULTS_DIR / "wallpaper_descriptions_multi_prompts-Portrait..b2.json",
]
BACKUP_MAIN = MAIN_FILE.with_suffix(".main_backup_before_merge.json")   # safety copy

# Optional: new output file if you want to test without overwriting
# OUTPUT_MERGED = RESULTS_DIR / "wallpaper_descriptions_multi_prompts-Portrait.merged.json"

print(f"Main file:   {MAIN_FILE}")
print(f"Batch files: {BATCH_FILES}\n")

# Step 1: Load main results (or start empty)
results = {}
if MAIN_FILE.exists():
    try:
        with open(MAIN_FILE, 'r', encoding='utf-8') as f:
            results = json.load(f)
        print(f"Loaded main file: {len(results):,} images already processed")
    except Exception as e:
        print(f"Error loading main file: {e}\nStarting with empty results.")
        results = {}
else:
    print("Main file not found → starting fresh.")

# Optional safety backup
if MAIN_FILE.exists():
    MAIN_FILE.rename(BACKUP_MAIN)
    print(f"→ Created backup: {BACKUP_MAIN.name}")

# Step 2: Merge from batch files (only add missing keys)
added_total = 0
for batch_path in BATCH_FILES:
    if not batch_path.exists():
        print(f"Batch file missing, skipping: {batch_path}")
        continue

    try:
        with open(batch_path, 'r', encoding='utf-8') as f:
            batch = json.load(f)

        added = 0
        for img_path, desc_data in batch.items():
            if img_path not in results:
                results[img_path] = desc_data
                added += 1

        print(f"From {batch_path.name}: added {added:,} new images")
        added_total += added
    except Exception as e:
        print(f"Error reading {batch_path}: {e}")

print(f"\nTotal new images added from batches: {added_total:,}")
print(f"Final total in results: {len(results):,} images")

unprocessed_files = [
    img_path for img_path in image_files
    if str(img_path.relative_to(BASE_DIR)) not in results
]
print(f"{len(unprocessed_files)} images left to process\n")

# ── Process ──
for i, img_path in enumerate(unprocessed_files, 1):
    rel_path = str(img_path.relative_to(BASE_DIR))
    print(f"[{i:4d}/{len(unprocessed_files):4d}] {img_path.name}")

    image_results = {}
    for model in MODELS:
        model_results = {}
        for prompt_config in PROMPTS:
            pname = prompt_config["name"]
            ptext = prompt_config["text"]
            print(f"  - {model} / {pname} ... ", end="", flush=True)
            desc = describe_image_with_model_and_prompt(img_path, model, ptext)
            model_results[pname] = desc
            print("done")
            time.sleep(0.4)  # gentle pause

        image_results[model] = model_results

    results[rel_path] = image_results



    # When saving (replace your existing save block):
    if i % CHECKPOINT_INTERVAL == 0 or i == len(unprocessed_files):
        # Step 3: Save merged result
        try:
            with open(MAIN_FILE, 'w', encoding='utf-8') as f:
                json.dump(results, f, indent=2, ensure_ascii=False)
            print(f"→ Successfully saved merged file: {MAIN_FILE}")
            exit(0)

            # Optional: also save to a separate file for verification
            # with open(OUTPUT_MERGED, 'w', encoding='utf-8') as f:
            #     json.dump(results, f, indent=2, ensure_ascii=False)
            # print(f"Also saved copy to: {OUTPUT_MERGED}")
        except Exception as e:
            print(f"Error saving merged file: {e}")

    time.sleep(1.5)  # between images

print(f"\nAll done! Results in: {OUTPUT_FILE}")
print("Structure: { \"relative/path.jpg\": { \"llava-llama3\": { \"detailed\": \"…\", \"keywords\": \"…\" }, … } }")
