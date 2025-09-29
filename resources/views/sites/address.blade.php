<div class="mb-3">
    <label>Street</label>
    <input type="text" class="form-control rounded-pill" id="{{ $id ?? 'site_street' }}" placeholder="Street">
</div>

<div class="mb-3 position-relative">
    <label>City, State</label>
    <input type="text" class="form-control rounded-pill" id="{{ $site_city ?? 'site_city' }}" placeholder="Type to search city" autocomplete="off">
    <ul id="{{ $site_city_list ?? 'site_city_list' }}" class="list-group position-absolute w-100 shadow" 
        style="z-index:1000; display:none; max-height:200px; overflow-y:auto; top:100%; left:0;">
    </ul>
</div>

<input type="hidden" id="site_state" name="state_id">
<input type="hidden" id="site_city_id" name="{{ $name ?? 'city_id' }}">

@push('scripts')
<script>
$(function(){
    const $cityInput = $('#{{ $cityId ?? 'site_city' }}');
    const $cityList  = $('#{{ $cityId ?? 'site_city_list' }}');
    const $stateInput = $('#site_state');
    const $cityIdInput = $('#site_city_id');

    let timer = null;

    function fetchCities(term){
        $.get("{{ oRoute('world.cities.search') }}", { q: term }, function(data){
            $cityList.empty();
            if(!data || data.length === 0){
                $cityList.hide();
                return;
            }
            data.forEach(c=>{
                $cityList.append(`<li class="list-group-item list-group-item-action city-option" data-id="${c.id}" data-state="${c.state_id}">${c.name}, ${c.state_name}</li>`);
            });
            $cityList.show();
        });
    }

    $cityInput.on('input', function(){
        const term = $(this).val().trim();
        clearTimeout(timer);

        if(term.length < 1){
            $cityList.hide().empty();
            $stateInput.val('');
            $cityIdInput.val('');
            return;
        }

        timer = setTimeout(()=> fetchCities(term), 250);
    });

    $cityList.on('click', '.city-option', function(){
        const text = $(this).text();
        const id = $(this).data('id');
        const stateId = $(this).data('state');

        $cityInput.val(text);
        $cityIdInput.val(id);
        $stateInput.val(stateId);
        $cityList.hide();
    });

    // Hide dropdown if clicked outside
    $(document).on('click', function(e){
        if(!$(e.target).closest($cityInput).length && !$(e.target).closest($cityList).length){
            $cityList.hide();
        }
    });

    // Arrow keys & enter
    $cityInput.on('keydown', function(e){
        const $items = $cityList.find('.city-option');
        if(!$items.length) return;

        let $active = $items.filter('.active');
        if(e.key === 'ArrowDown'){
            e.preventDefault();
            const $next = $active.length ? $active.next() : $items.first();
            $items.removeClass('active');
            $next.addClass('active');
        }
        if(e.key === 'ArrowUp'){
            e.preventDefault();
            const $prev = $active.length ? $active.prev() : $items.last();
            $items.removeClass('active');
            $prev.addClass('active');
        }
        if(e.key === 'Enter'){
            e.preventDefault();
            if($active.length){
                $active.click();
            }
        }
    });

    // Split last saved address
    window.splitAddress = function(address){
        if(!address) return {street:'', city:'', state:''};
        const parts = address.split(',');
        const len = parts.length;
        const state = parts[len-1]?.trim() ?? '';
        const city  = parts[len-2]?.trim() ?? '';
        const street = parts.slice(0,len-2).join(',').trim();
        return {street, city, state};
    };

    // Merge street + city
    window.mergeAddress = function(){
        const street = $(`#{{ $id ?? 'site_street' }}`).val();
        const city = $cityInput.val();
        return [street, city].filter(Boolean).join(', ');
    };
});
</script>
@endpush
