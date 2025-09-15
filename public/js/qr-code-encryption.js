async function encryptAES128CBC(text, key) {
    const encoder = new TextEncoder();
    const data = encoder.encode(text);

    // Ensure key is exactly 16 bytes
    if (key.length !== 16) {
        throw new Error("AES key must be exactly 16 characters for AES-128");
    }

    const iv = crypto.getRandomValues(new Uint8Array(16));

    const cryptoKey = await crypto.subtle.importKey(
        "raw",
        encoder.encode(key),
        { name: "AES-CBC" },
        false,
        ["encrypt"]
    );

    const encrypted = await crypto.subtle.encrypt(
        { name: "AES-CBC", iv },
        cryptoKey,
        data
    );

    const buffer = new Uint8Array(encrypted);
    const combined = new Uint8Array(iv.length + buffer.length);
    combined.set(iv);
    combined.set(buffer, iv.length);

    return btoa(String.fromCharCode(...combined));
}

async function generateEncryptedQR(canvas, dataObj, key, size = 150) {
    const json = JSON.stringify(dataObj);
    const encrypted = await encryptAES128CBC(json, key);

    new QRious({
        element: canvas,
        value: encrypted,
        size
    });
}

async function generateAllQRs(selector, type, key, size = 150) {
    const cards = document.querySelectorAll(selector);
    const promises = Array.from(cards).map(card =>
        generateEncryptedQR(
            card.querySelector('canvas'),
            { type, type_id: card.dataset.id, name: card.dataset.name },
            key,
            size
        )
    );
    await Promise.all(promises);
}
