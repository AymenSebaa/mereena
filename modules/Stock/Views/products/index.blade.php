@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="mobile-padding">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb glass-card p-3 mb-4">
                <li class="breadcrumb-item active">Products</li>
            </ol>
        </nav>

        <!-- Search + Add -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <input type="text" id="productSearch" class="form-control" placeholder="Search products...">
            <button class="btn btn-primary w-50 m-3" data-bs-toggle="modal" data-bs-target="#upsertProductModal">
                <i class="bi bi-plus-circle me-2"></i> New
            </button>
        </div>

        <!-- Products Grid -->
        <div id="products-container" class="dls-container"></div>

        <!-- Pagination -->
        <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
    </div>

    <!-- Upsert Modal -->
    <div class="modal fade" id="upsertProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content glass-card">
                <div class="modal-header">
                    <h5 class="modal-title">Upsert Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="upsertProductForm">
                    @csrf
                    <input type="hidden" name="id" id="product_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Images</label>
                            <input type="file" class="form-control" name="images[]" id="product_images" accept="image/*"
                                multiple>
                            <div id="preview_images" class="d-flex flex-wrap gap-2 mt-2"></div>
                        </div>

                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" id="product_name" required>
                        </div>
                        <div class="mb-3">
                            <label>SKU</label>
                            <input type="text" class="form-control" name="sku" id="product_sku" required>
                        </div>
                        <div class="mb-3">
                            <label>Category</label>
                            <input type="text" class="form-control" name="category" id="product_category">
                        </div>
                        <div class="mb-3">
                            <label>Brand</label>
                            <input type="text" class="form-control" name="brand" id="product_brand">
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <textarea class="form-control" name="description" id="product_description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content glass-card">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="deleteProductForm">
                    @csrf @method('delete')
                    <div class="modal-body">
                        <p>Are you sure you want to delete this product?</p>
                        <input type="hidden" id="delete_product_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('product_images').addEventListener('change', function(e) {
            const preview = document.getElementById('preview_images');
            preview.innerHTML = '';

            Array.from(e.target.files).forEach((file, index) => {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgWrapper = document.createElement('div');
                    imgWrapper.classList.add('position-relative');
                    imgWrapper.style.width = '100px';
                    imgWrapper.style.height = '100px';

                    imgWrapper.innerHTML = `
                <img src="${e.target.result}" class="img-thumbnail w-100 h-100" style="object-fit:cover;">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0"
                        onclick="removeSelectedImage(${index})">&times;</button>
            `;
                    preview.appendChild(imgWrapper);
                };
                reader.readAsDataURL(file);
            });
        });

        let removedIndexes = [];

        function removeSelectedImage(index) {
            removedIndexes.push(index);

            const input = document.getElementById('product_images');
            const dt = new DataTransfer();

            Array.from(input.files).forEach((file, i) => {
                if (!removedIndexes.includes(i)) {
                    dt.items.add(file);
                }
            });

            input.files = dt.files;
            input.dispatchEvent(new Event('change')); // re-render preview
        }


        let products = @json($products ?? []);
        let currentPage = 1;
        const perPage = 12;

        function productCard(product) {
            let images = (product.images && product.images.length > 0) ?
                product.images :
                ['https://via.placeholder.com/150?text=No+Image'];

            let carouselId = `carousel_${product.id}`;
            let indicators = '';
            let slides = '';

            images.forEach((img, i) => {
                indicators += `
            <button type="button" data-bs-target="#${carouselId}" data-bs-slide-to="${i}" 
                ${i===0 ? 'class="active"' : ''} aria-current="true"></button>`;
                slides += `
            <div class="carousel-item ${i===0 ? 'active' : ''}">
                <img src="/${img}" class="d-block w-100" style="height:150px;object-fit:cover;">
            </div>`;
            });

            return `
                <div class="dl-card" id="product_${product.id}">
                    <div id="${carouselId}" class="carousel slide mb-2" data-bs-ride="carousel">
                        <div class="carousel-indicators">${indicators}</div>
                        <div class="carousel-inner">${slides}</div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <h6>${product.name}</h6>
                    </div>
                    <div class="small mb-2">
                        <i class="bi bi-upc"></i> ${product.sku ?? '-'}<br>
                        <i class="bi bi-tags"></i> ${product.category ?? '-'}<br>
                        <i class="bi bi-box"></i> ${product.brand ?? '-'}<br>
                        <i class="bi bi-currency-dollar"></i> ${product.price ?? 0}
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <button class="btn btn-sm btn-outline-warning"
                            onclick="openEdit(${product.id}, '${product.name}', '${product.sku}', '${product.category ?? ''}', '${product.brand ?? ''}', '${product.price ?? 0}', \`${product.description ?? ''}\`)">
                            <i class="bi bi-pencil-square"></i> 
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="openDelete(${product.id})">
                            <i class="bi bi-trash"></i> 
                        </button>
                    </div>
                </div>
            `;
        }



        function renderProducts() {
            const container = document.getElementById('products-container');
            container.innerHTML = '';
            const start = (currentPage - 1) * perPage;
            const pageItems = products.slice(start, start + perPage);

            if (pageItems.length === 0) {
                container.innerHTML = `<div class="dl-card">No products found</div>`;
                return;
            }

            pageItems.forEach(product => {
                container.insertAdjacentHTML('beforeend', productCard(product));
            });
        }

        function renderPagination() {
            const totalPages = Math.ceil(products.length / perPage);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';
            if (totalPages <= 1) return;
            for (let i = 1; i <= totalPages; i++) {
                pagination.insertAdjacentHTML('beforeend', `
            <button class="btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-light'} mx-1"
                onclick="goToPage(${i})">${i}</button>
        `);
            }
        }

        function goToPage(page) {
            currentPage = page;
            renderProducts();
            renderPagination();
        }

        // ---- Upsert ----
        document.getElementById('upsertProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            fetch("{{ route('products.upsert') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.result) {
                        let existing = document.getElementById(`product_${data.data.id}`);
                        if (existing) {
                            existing.outerHTML = productCard(data.data);
                        } else {
                            document.getElementById('products-container')
                                .insertAdjacentHTML('afterbegin', productCard(data.data));
                            products.unshift(data.data);
                        }
                        bootstrap.Modal.getInstance(document.getElementById('upsertProductModal')).hide();
                    }
                })
                .catch(err => console.error(err));
        });

        // ---- Delete ----
        function openDelete(id) {
            document.getElementById('delete_product_id').value = id;
            new bootstrap.Modal(document.getElementById('deleteProductModal')).show();
        }

        document.getElementById('deleteProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let id = document.getElementById('delete_product_id').value;

            fetch("{{ route('products.delete', ':id') }}".replace(':id', id), {
                    method: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.result) {
                        document.getElementById(`product_${id}`).remove();
                        products = products.filter(p => p.id !== parseInt(id));
                        bootstrap.Modal.getInstance(document.getElementById('deleteProductModal')).hide();
                    }
                })
                .catch(err => console.error(err));
        });

        // ---- Edit ----
        function openEdit(id, name, sku, category, brand, price, description) {
            document.getElementById('product_id').value = id;
            document.getElementById('product_name').value = name;
            document.getElementById('product_sku').value = sku;
            document.getElementById('product_category').value = category;
            document.getElementById('product_brand').value = brand;
            document.getElementById('product_price').value = price;
            document.getElementById('product_description').value = description;
            new bootstrap.Modal(document.getElementById('upsertProductModal')).show();
        }

        // ---- Search ----
        document.getElementById('productSearch').addEventListener('input', function(e) {
            const q = e.target.value.toLowerCase();
            products = @json($products ?? []).filter(p =>
                (p.name ?? '').toLowerCase().includes(q) ||
                (p.sku ?? '').toLowerCase().includes(q) ||
                (p.category ?? '').toLowerCase().includes(q) ||
                (p.brand ?? '').toLowerCase().includes(q)
            );
            currentPage = 1;
            renderProducts();
            renderPagination();
        });

        // Init
        renderProducts();
        renderPagination();
    </script>
@endpush
