#!/bin/bash
cd /var/www/nicer.app-5.10.z/domains/nicer.app/NicerAppWebOS/scripts.maintenance
rm debug.txt
source venv/bin/activate
pip install ollama
ollama pull llava-llama3
ollama pull llava:7b
ollama pull bakllava
ollama pull moondream
echo "Success!" > debug.txt
