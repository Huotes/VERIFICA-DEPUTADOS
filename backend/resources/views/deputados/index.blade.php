@extends('layouts.app')

@section('content')
<div id="deputados-container" class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4">
    @include('components.deputados-list', ['deputados' => $deputados])
</div>

<div id="loading" class="text-center py-4 hidden">
    <p>Carregando mais deputados...</p>
</div>

<script>
let offset = 30;
let loading = false;

window.addEventListener('scroll', async () => {
    if (loading) return;

    const scrollPosition = window.innerHeight + window.scrollY;
    const bottom = document.body.offsetHeight - 100;

    if (scrollPosition >= bottom) {
        loading = true;
        document.getElementById('loading').classList.remove('hidden');

        const response = await fetch(`/deputados/load?offset=${offset}`);
        const html = await response.text();

        document.getElementById('deputados-container').insertAdjacentHTML('beforeend', html);
        document.getElementById('loading').classList.add('hidden');

        offset += 30;
        loading = false;
    }
});
</script>
@endsection
