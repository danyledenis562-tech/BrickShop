import io
import urllib.request
from pathlib import Path
from PIL import Image

out_dir = Path(r"C:\\xampp\\htdocs\\lego-shop3\\public\\images\\categories\\real")
out_dir.mkdir(parents=True, exist_ok=True)

items = {
    "lego-city": "https://www.lego.com/cdn/cs/set/assets/blt402b1fe599d2d64e/60419_alt1.png?fit=bounds&format=png&width=900&height=700&dpr=1",
    "lego-star-wars": "https://www.lego.com/cdn/cs/set/assets/blt3e07af4c83a87efd/75355.png?fit=bounds&format=png&width=900&height=700&dpr=1",
    "lego-technic": "https://www.lego.com/cdn/cs/set/assets/blt519dac201a3dd4c1/42172.png?fit=bounds&format=png&width=900&height=700&dpr=1",
    "lego-friends": "https://www.lego.com/cdn/cs/set/assets/blt8adecb6d9afbf65b/41748_Feature_thumbnail.jpg?fit=bounds&format=jpg&quality=80&width=900&height=700&dpr=1",
    "lego-creator": "https://www.lego.com/cdn/cs/set/assets/bltf2b396a7c0264a4a/31136_v1_thumbnail.jpg?fit=bounds&format=jpg&quality=80&width=900&height=700&dpr=1",
    "lego-ninjago": "https://www.lego.com/cdn/cs/set/assets/blt7f92643d208fcc7c/71799_v1_thumbnail.png?fit=bounds&format=png&width=900&height=700&dpr=1",
}

def download(url: str) -> Image.Image:
    with urllib.request.urlopen(url) as resp:
        data = resp.read()
    return Image.open(io.BytesIO(data)).convert("RGBA")

def remove_white(img: Image.Image, threshold=245):
    pixels = img.load()
    w, h = img.size
    for y in range(h):
        for x in range(w):
            r, g, b, a = pixels[x, y]
            if r >= threshold and g >= threshold and b >= threshold:
                pixels[x, y] = (r, g, b, 0)
    return img

for slug, url in items.items():
    img = download(url)
    img = remove_white(img)
    out_path = out_dir / f"{slug}.png"
    img.save(out_path, "PNG")
    print(f"Saved {out_path}")
