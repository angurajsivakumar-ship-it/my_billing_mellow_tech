<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    $(document).ready(function() {
        var baseUrl = $('meta[name="baseUrl"]').attr('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        const token = document.querySelector('meta[name="_token"]')?.content;
        if (token) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
        }
    });
</script>
