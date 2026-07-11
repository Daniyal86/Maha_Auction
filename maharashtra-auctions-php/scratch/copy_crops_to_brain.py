import shutil
import os

brain_dir = r"C:\Users\ITS\.gemini\antigravity\brain\4cb57857-f728-4ea0-bf7c-904ae932b5a6"
src_pattern = r"c:\Users\ITS\Desktop\DuoSTack\PRP\maharashtra-auctions-php\assets\COfounder\for_legal_crop_{}.jpg"

for i in range(5):
    src = src_pattern.format(i)
    dst = os.path.join(brain_dir, f"for_legal_crop_{i}.jpg")
    shutil.copy2(src, dst)
    print(f"Copied {src} to {dst}")
