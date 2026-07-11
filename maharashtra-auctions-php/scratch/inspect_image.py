from PIL import Image

img_path = r"c:\Users\ITS\Desktop\DuoSTack\PRP\maharashtra-auctions-php\assets\COfounder\for legal.jpeg"
with Image.open(img_path) as img:
    print(f"Format: {img.format}, Size: {img.size}, Mode: {img.mode}")
