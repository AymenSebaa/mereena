@props([
    'id' => '',
    'name' => '',
    'label' => '',
    'placeholder' => 'Type to search...',
    'fetchUrl' => '',
    'parentName' => '',
])

@php
    $unique = uniqid();
    $inputId = $id ?? 'select_search_input_' . $unique;
    $listId = 'select_search_list_' . $unique;
    $hiddenId = $hiddenId ?? 'select_search_hidden_' . $unique;
@endphp

<div class="mb-3 position-relative select-search-component" id="{{ $inputId }}_wrapper">
    <label for="{{ $inputId }}">{{ $label }}</label>
    <input type="text" class="form-control rounded-pill" id="{{ $inputId }}" placeholder="{{ $placeholder }}"
        autocomplete="off" data-parent="{{ $parentName }}">
    <ul id="{{ $listId }}" class="list-group position-absolute w-100 shadow"
        style="z-index:1000; display:none; max-height:200px; overflow-y:auto; top:100%; left:0;"></ul>
    <input type="hidden" id="{{ $hiddenId }}" name="{{ $name }}">
</div>

@push('scripts')
    <script>
        (function() {
            const $wrapper = $('#{{ $inputId }}_wrapper');
            const $input = $wrapper.find('#{{ $inputId }}');
            const $list = $wrapper.find('#{{ $listId }}');
            const $hidden = $wrapper.find('#{{ $hiddenId }}');
            let timer = null;

            function fetchItems(term) {
                if (!term) return $list.hide().empty();
                const parentName = $input.data('parent');

                $.post("{{ $fetchUrl }}", {
                    q: term,
                    parent_name: parentName,
                    _token: '{{ csrf_token() }}'
                }, function(data) {
                    $list.empty();
                    if (!data || data.length === 0) {
                        $list.hide();
                        return;
                    }
                    data.forEach(item => {
                        $list.append(
                            `<li class="list-group-item list-group-item-action option-item" data-id="${item.id}">${item.name}</li>`
                        );
                    });
                    $list.show();
                });
            }

            $input.on('input', function() {
                const term = $(this).val().trim();
                clearTimeout(timer);
                timer = setTimeout(() => fetchItems(term), 250);
            });

            $list.on('click', '.option-item', function() {
                const text = $(this).text();
                const id = $(this).data('id');
                $input.val(text);
                $hidden.val(id);
                $list.hide();
            });

            $(document).on('click', function(e) {
                if (!$wrapper.is(e.target) && $wrapper.has(e.target).length === 0) {
                    $list.hide();
                }
            });

            $input.on('keydown', function(e) {
                const $items = $list.find('.option-item');
                if (!$items.length) return;

                let $active = $items.filter('.active');
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    const $next = $active.length ? $active.next() : $items.first();
                    $items.removeClass('active');
                    $next.addClass('active');
                }
                if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const $prev = $active.length ? $active.prev() : $items.last();
                    $items.removeClass('active');
                    $prev.addClass('active');
                }
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if ($active.length) $active.click();
                }
            });

            // NEW: global function to set value
            window.setSelectSearchValue = function(wrapperId, id, name) {
                const $wrap = $('#' + wrapperId);
                $wrap.find('input[type=hidden]').val(id);
                $wrap.find('input[type=text]').val(name);
            };
        })();
    </script>
@endpush
