@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="mobile-padding">

        <!-- Search + Add -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <input type="text" id="productSearch" class="form-control rounded-pill" placeholder="Search products...">
            @include('stock::products.upsert-modal')
        </div>

        <!-- Products Grid -->
        <div id="products-container" class="dls-container"></div>

        <!-- Pagination -->
        <div id="pagination" class="mt-4 d-flex justify-content-center"></div>
    </div>

    @include('stock::products.delete-modal')
@endsection

@push('scripts')
    <script>
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
                console.log('img', img);

                indicators += `
                <button type="button" data-bs-target="#${carouselId}" data-bs-slide-to="${i}" 
                    ${i===0 ? 'class="active"' : ''} aria-current="true"></button>`;
                    slides += `
                <div class="carousel-item ${i===0 ? 'active' : ''}">
                    <img src="{{env('APP_URL')}}/${img}" class="d-block w-100" style="height:150px;object-fit:cover;">
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
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6>${product.name}</h6>
                        <div class="mt-3 d-flex gap-2">
                            <button class="btn btn-sm btn-outline-warning rounded-pill"
                                onclick="openEdit(${product.id}, '${product.name}', '${product.sku}', '${product.category.id ?? ''}', '${product.brand ?? ''}', '${product.price ?? 0}', \`${product.description ?? ''}\`)">
                                <i class="bi bi-pencil-square"></i> 
                            </button>
                            <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="openDelete(${product.id})">
                                <i class="bi bi-trash"></i> 
                            </button>
                        </div>
                    </div>
                    <div class="small mb-2">
                        <i class="bi bi-upc"></i> ${product.sku ?? '-'}<br>
                        <i class="bi bi-tags"></i> ${product.category.name ?? '-'}<br>
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

        // ---- Search ----
        document.getElementById('productSearch').addEventListener('input', function(e) {
            const q = e.target.value.toLowerCase();
            products = @json($products ?? []).filter(p =>
                (p.name ?? '').toLowerCase().includes(q) ||
                (p.sku ?? '').toLowerCase().includes(q) ||
                (p.category.name ?? '').toLowerCase().includes(q) ||
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
