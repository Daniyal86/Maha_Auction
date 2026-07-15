from PIL import Image

img_path = r"c:\Users\ITS\Desktop\DuoSTack\PRP\maharashtra-auctions-php\assets\COfounder\for legal.jpeg"
out_path = r"c:\Users\ITS\Desktop\DuoSTack\PRP\maharashtra-auctions-php\assets\COfounder\for legal.png"

with Image.open(img_path) as img:
    # Crop 3 coordinates: Top = 450, Height = Width = 2483
    cropped = img.crop((0, 450, 2483, 2933))
    
    # Save as PNG with optimal quality/compression
    cropped.save(out_path, "PNG")
    print(f"Successfully saved final cropped image to {out_path}")
