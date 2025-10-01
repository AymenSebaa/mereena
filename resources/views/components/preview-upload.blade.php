@props([
    'name' => 'images',
    'label' => 'Images',
    'multiple' => true,
    'accept' => 'image/*',
    'maxSize' => 512,
])

<div class="mb-3 preview-upload-component" data-name="{{ $name }}">
    <label>{{ $label }}</label>
    <input type="file" class="form-control rounded-pill image-input"
        accept="{{ $accept }}"
        {{ $multiple ? 'multiple' : '' }}>
    <div class="preview-container d-flex flex-wrap gap-2 mt-2"></div>
</div>

@push('scripts')
<script>
    function resizeAndConvertToBase64(file, maxSize = {{ $maxSize }}) {
        return new Promise(resolve => {
            const reader = new FileReader();
            reader.onload = e => {
                const img = new Image();
                img.onload = () => {
                    let [w, h] = [img.width, img.height];
                    if (w > h && w > maxSize) {
                        h *= maxSize / w; w = maxSize;
                    } else if (h > maxSize) {
                        w *= maxSize / h; h = maxSize;
                    }
                    const canvas = document.createElement("canvas");
                    canvas.width = w; canvas.height = h;
                    canvas.getContext("2d").drawImage(img, 0, 0, w, h);
                    resolve(canvas.toDataURL(file.type, 0.9));
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    function initUploader($wrapper) {
        const $input = $wrapper.find(".image-input");
        const $preview = $wrapper.find(".preview-container");
        const fieldName = $wrapper.data("name");
        let files = [];

        const addFile = async file => {
            const base64 = await resizeAndConvertToBase64(file);
            files.push(base64);

            const $imgBox = $(`
                <div class="position-relative">
                    <img src="${base64}" class="rounded border" style="width:100px;height:100px;object-fit:cover;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0">&times;</button>
                    <input type="hidden" name="${fieldName}[]" value="${base64}">
                </div>
            `);

            $imgBox.find("button").on("click", () => {
                $imgBox.remove();
                files = files.filter(f => f !== base64);
            });

            $preview.append($imgBox);
        };

        $input.on("change", async function() {
            for (const file of this.files) await addFile(file);
            this.value = "";
        });

        return {
            reset: () => {
                files = [];
                $preview.empty();
                $input.val("");
            }
        };
    }

    $(function() {
        if (!window.uploaders) window.uploaders = [];
        $(".preview-upload-component").each(function() {
            window.uploaders.push(initUploader($(this)));
        });
    });
</script>
@endpush
