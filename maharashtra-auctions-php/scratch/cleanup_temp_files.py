import os

assets_dir = r"c:\Users\ITS\Desktop\DuoSTack\PRP\maharashtra-auctions-php\assets\COfounder"
for i in range(5):
    filepath = os.path.join(assets_dir, f"for_legal_crop_{i}.jpg")
    if os.path.exists(filepath):
        os.remove(filepath)
        print(f"Removed {filepath}")
