"""
FG brand logo generator for plg_system_fgremovegenerator.
Teal squircle background (#105060 -> #1A6877 gradient), coral (#FF6B4A) accent.
Motif: a "tag" shape (meta/generator tag) with a coral "no/remove" slash - representing
removal of the generator meta tag / fingerprinting headers.
Flat rendering, no drop shadow (per FG standalone-logo convention), strictly binary alpha.
"""
from PIL import Image, ImageDraw
import math

SIZE = 512
TEAL_TOP = (16, 80, 96)      # #105060
TEAL_BOTTOM = (26, 104, 119) # #1A6877
CORAL = (255, 107, 74)       # #FF6B4A
WHITE = (255, 255, 255)

def rounded_rect_mask(size, radius):
    mask = Image.new("L", (size, size), 0)
    d = ImageDraw.Draw(mask)
    d.rounded_rectangle([0, 0, size - 1, size - 1], radius=radius, fill=255)
    return mask

def make_gradient(size, top, bottom):
    grad = Image.new("RGB", (1, size), 0)
    for y in range(size):
        t = y / (size - 1)
        r = int(top[0] + (bottom[0] - top[0]) * t)
        g = int(top[1] + (bottom[1] - top[1]) * t)
        b = int(top[2] + (bottom[2] - top[2]) * t)
        grad.putpixel((0, y), (r, g, b))
    return grad.resize((size, size))

def main():
    bg = make_gradient(SIZE, TEAL_TOP, TEAL_BOTTOM).convert("RGBA")
    mask = rounded_rect_mask(SIZE, radius=100)

    canvas = Image.new("RGBA", (SIZE, SIZE), (0, 0, 0, 0))
    canvas.paste(bg, (0, 0), mask)

    draw = ImageDraw.Draw(canvas)

    # --- Tag icon (meta/generator tag motif) ---
    cx, cy = SIZE * 0.42, SIZE * 0.52
    tag_w, tag_h = 200, 140
    # Tag body: rounded rect with a pointed left notch (classic price/meta tag shape)
    pts = [
        (cx - tag_w/2 + 40, cy - tag_h/2),
        (cx + tag_w/2, cy - tag_h/2),
        (cx + tag_w/2, cy + tag_h/2),
        (cx - tag_w/2 + 40, cy + tag_h/2),
        (cx - tag_w/2, cy),
    ]
    draw.polygon(pts, fill=WHITE)
    # small hole in the tag
    hole_r = 14
    hole_cx, hole_cy = cx - tag_w/2 + 62, cy
    draw.ellipse(
        [hole_cx - hole_r, hole_cy - hole_r, hole_cx + hole_r, hole_cy + hole_r],
        fill=TEAL_TOP
    )

    # --- Coral "no/remove" prohibition ring + slash, overlapping bottom-right of tag ---
    ring_cx, ring_cy = SIZE * 0.64, SIZE * 0.66
    ring_r = 92
    ring_width = 26
    bbox = [ring_cx - ring_r, ring_cy - ring_r, ring_cx + ring_r, ring_cy + ring_r]
    draw.ellipse(bbox, outline=CORAL, width=ring_width)
    # diagonal slash through the ring (45 degrees)
    slash_len = ring_r * 1.35
    angle = math.radians(45)
    dx = slash_len * math.cos(angle)
    dy = slash_len * math.sin(angle)
    draw.line(
        [(ring_cx - dx, ring_cy + dy), (ring_cx + dx, ring_cy - dy)],
        fill=CORAL, width=ring_width
    )

    # Ensure strictly binary alpha (no soft edges bleeding outside the squircle mask)
    r, g, b, a = canvas.split()
    a = a.point(lambda p: 255 if p > 127 else 0)
    canvas = Image.merge("RGBA", (r, g, b, a))

    canvas.save("/home/claude/fgremovegenerator_build/logo.png")
    canvas.resize((256, 256), Image.LANCZOS).save("/home/claude/fgremovegenerator_build/logo_256.png")
    print("Logo generated.")

if __name__ == "__main__":
    main()
