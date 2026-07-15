from PIL import Image

img_path = r"c:\Users\ITS\Desktop\DuoSTack\PRP\maharashtra-auctions-php\assets\COfounder\for legal.jpeg"
with Image.open(img_path) as img:
    w, h = img.size
    
    # Square crop size is w (2483)
    crop_size = w
    
    # We will try different starting y positions
    y_offsets = [0, 150, 300, 450, 600]
    
    for i, y_start in enumerate(y_offsets):
        y_end = y_start + crop_size
        if y_end <= h:
            cropped = img.crop((0, y_start, w, y_end))
            out_path = f"c:\\Users\\ITS\\Desktop\\DuoSTack\\PRP\\maharashtra-auctions-php\\assets\\COfounder\\for_legal_crop_{i}.jpg"
            cropped.save(out_path, "JPEG", quality=95)
            print(f"Saved crop {i} starting at y={y_start} to {out_path}")
