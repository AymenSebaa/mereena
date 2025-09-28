<div class="mb-3">
    <label>{{ $label ?? 'Images' }}</label>
    <input type="file" class="form-control rounded-pill image-input" name="images[]" accept="image/*" multiple>
    <div class="preview-container d-flex flex-wrap gap-2 mt-2"></div>
</div>

<script>
    function resizeImage(file, maxSize = 512) {
        return new Promise(resolve => {
            const reader = new FileReader();
            reader.onload = e => {
                $("<img>").on("load", function () {
                    let [w, h] = [this.width, this.height];
                    if (w > h && w > maxSize) { h *= maxSize / w; w = maxSize; }
                    else if (h > maxSize) { w *= maxSize / h; h = maxSize; }

                    const canvas = $("<canvas>")[0];
                    canvas.width = w; canvas.height = h;
                    canvas.getContext("2d").drawImage(this, 0, 0, w, h);

                    canvas.toBlob(blob => {
                        resolve(new File([blob], file.name, { type: file.type }));
                    }, file.type, 0.9);
                }).attr("src", e.target.result);
            };
            reader.readAsDataURL(file);
        });
    }

    function initUploader($wrapper) {
        const $input = $wrapper.find(".image-input");
        const $preview = $wrapper.find(".preview-container");
        let files = [];

        $input.on("change", async function () {
            for (const file of this.files) {
                if (files.find(f => f.name === file.name && f.size === file.size)) continue;

                const resized = await resizeImage(file);
                files.push(resized);

                const url = URL.createObjectURL(resized);
                const $imgBox = $(`
                    <div class="position-relative">
                        <img src="${url}" class="rounded border" style="width:100px;height:100px;object-fit:cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0">&times;</button>
                    </div>
                `);

                $imgBox.find("button").on("click", () => {
                    $imgBox.remove();
                    files = files.filter(f => f !== resized);
                });

                $preview.append($imgBox);
            }
            this.value = "";
        });

        return {
            getFiles: () => files,
            reset: () => { files = []; $preview.empty(); $input.val(""); }
        };
    }

    $(function () {
        window.uploaders = [];
        $(".mb-3:has(.image-input)").each(function () {
            window.uploaders.push(initUploader($(this)));
        });
    });
</script>
