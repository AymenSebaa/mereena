<div class="modal fade" id="upsertSiteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-card">
            <div class="modal-header">
                <h5 class="modal-title">Upsert Site</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="upsertSiteForm">
                    @csrf
                    <input type="hidden" name="id" id="site_id">
                    <input type="hidden" name="lat" id="site_lat">
                    <input type="hidden" name="lng" id="site_lng">
                    <input type="hidden" name="geofence" id="site_geofence">
                    
                    <div class="mb-3">
                        <label>Site Name</label>
                        <input type="text" class="form-control rounded-pill" name="name" id="site_name" required placeholder="Site name">
                    </div>

                    @include('sites.address', ['id'=>'site_street','site_city'=>'site_city','site_city_list'=>'site_city_list','name'=>'city_id'])
                    @include('sites.geofence', ['mapId'=>'site-map'])

                    <div class="modal-footer">
                        <button class="btn btn-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success rounded-pill">Save Site</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let siteModal;

function openNewSite() {
    $('#upsertSiteForm')[0].reset();
    $('#site_id').val('');
    $('#site_city').val(null).trigger('change');
    $('#site_state').val('');
    siteModal = new bootstrap.Modal(document.getElementById('upsertSiteModal'));
    siteModal.show();
}

function openEditSite(site) {
    $('#upsertSiteForm')[0].reset();
    $('#site_id').val(site.id);
    $('#site_name').val(site.name);
    $('#site_lat').val(site.lat);
    $('#site_lng').val(site.lng);
    $('#site_geofence').val(site.geofence ?? '');

    const parts = splitAddress(site.address);
    $('#site_street').val(parts.street);

    if(parts.city) {
        $('#site_city').val(parts.city + ', ' + parts.state); // set input text
        $('#site_city_id').val(site.city_id);                // hidden input
        $('#site_state').val(site.state_id);                // hidden input
    }

    siteModal = new bootstrap.Modal(document.getElementById('upsertSiteModal'));
    siteModal.show();
}


// Submit
$('#upsertSiteForm').on('submit', function(e){
    e.preventDefault();
    const combinedAddress = mergeAddress();
    $(this).append('<input type="hidden" name="address" value="'+combinedAddress+'">');

    $.post("{{ oRoute('sites.upsert') }}", $(this).serialize(), function(res){
        if(res.result){
            siteModal.hide();
            fetchSites($('#site-search').val());

            showToast(res.message, 'success');
        } 
    }).fail(function(xhr) {
        showToast(xhr.responseJSON?.message || "Save failed", "error");
    });
});
</script>
@endpush
