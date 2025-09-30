@props([
    'name' => 'images',
    'label' => 'Images',
    'multiple' => true,
    'accept' => 'image/*',
    'maxSize' => 512,
])

<div class="mb-3 preview-upload-component">
    <label>{{ $label }}</label>
    <input type="file" class="form-control rounded-pill image-input"
        name="{{ $name }}{{ $multiple ? '[]' : '' }}" accept="{{ $accept }}"
        {{ $multiple ? 'multiple' : '' }}>
    <div class="preview-container d-flex flex-wrap gap-2 mt-2"></div>
</div>

@push('scripts')
    <script>
        function resizeImage(file, maxSize = {{ $maxSize }}) {
            return new Promise(resolve => {
                const reader = new FileReader();
                reader.onload = e => {
                    $("<img>").on("load", function() {
                        let [w, h] = [this.width, this.height];
                        if (w > h && w > maxSize) {
                            h *= maxSize / w;
                            w = maxSize;
                        } else if (h > maxSize) {
                            w *= maxSize / h;
                            h = maxSize;
                        }

                        const canvas = $("<canvas>")[0];
                        canvas.width = w;
                        canvas.height = h;
                        canvas.getContext("2d").drawImage(this, 0, 0, w, h);

                        canvas.toBlob(blob => {
                            resolve(new File([blob], file.name, {
                                type: file.type
                            }));
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

            const addFile = async file => {
                let fileObj = file;

                // If it's a string (URL), convert to File-like object
                if (typeof file === "string") {
                    const response = await fetch(file);
                    const blob = await response.blob();
                    fileObj = new File([blob], file.split("/").pop(), {
                        type: blob.type
                    });
                }

                // skip duplicates
                if (files.find(f => f.name === fileObj.name && f.size === fileObj.size)) return;

                const resized = await resizeImage(fileObj, 512);
                files.push(resized);

                const url = typeof file === "string" ? file : URL.createObjectURL(resized);
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
            };

            $input.on("change", async function() {
                for (const file of this.files) await addFile(file);
                this.value = "";
            });

            return {
                getFiles: () => files,
                reset: () => {
                    files = [];
                    $preview.empty();
                    $input.val("");
                },
                removeAllFiles: function() {
                    this.reset();
                },
                addMockFile: async file => await addFile(file)
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
