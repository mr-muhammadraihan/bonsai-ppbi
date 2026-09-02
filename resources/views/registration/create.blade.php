<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Partisipasi | Bonsai</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 text-stone-900">
    <div class="relative overflow-hidden bg-emerald-950">
        <div class="absolute -right-24 -top-32 h-80 w-80 rounded-full bg-amber-300/20 blur-3xl"></div>
        <div class="mx-auto max-w-6xl px-6 py-16 sm:px-8 lg:px-12">
            <p class="mb-4 text-sm font-semibold uppercase tracking-[0.28em] text-amber-300">Bonsai Exhibition</p>
            <h1 class="max-w-3xl font-serif text-4xl font-semibold tracking-tight text-white sm:text-6xl">Daftarkan partisipasi dan bonsai Anda.</h1>
            <p class="mt-6 max-w-2xl text-base leading-7 text-emerald-100 sm:text-lg">Lengkapi data peserta dan semua bonsai dalam satu formulir. Foto asli akan diproses otomatis dengan kualitas tinggi.</p>
        </div>
    </div>

    <main class="mx-auto max-w-6xl px-6 py-10 sm:px-8 lg:px-12">
        @if (session('registration_success'))
            <div class="mb-8 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800" role="status">
                {{ session('registration_success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-8 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800" role="alert">
                <p class="font-semibold">Periksa kembali data registrasi.</p>
                <ul class="mt-2 list-inside list-disc text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('registration.store') }}" enctype="multipart/form-data" class="space-y-8" id="registration-form">
            @csrf

            <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-stone-200 sm:p-8">
                <div class="mb-7 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">01 / Partisipasi</p>
                        <h2 class="mt-2 text-2xl font-semibold">Data peserta</h2>
                    </div>
                    <span class="hidden text-sm text-stone-500 sm:block">Nama peserta wajib diisi</span>
                </div>
                <div class="grid gap-5 md:grid-cols-2">
                    <x-registration-input name="name" label="Nama lengkap" required />
                </div>
            </section>

            <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-stone-200 sm:p-8">
                <div class="mb-7 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">02 / Koleksi</p>
                        <h2 class="mt-2 text-2xl font-semibold">Data bonsai</h2>
                        <p class="mt-2 text-sm text-stone-500">Tambahkan satu atau lebih bonsai untuk peserta ini.</p>
                    </div>
                    <button type="button" id="add-bonsai" class="rounded-full bg-emerald-800 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">+ Tambah bonsai</button>
                </div>

                <div id="bonsai-list" class="space-y-6">
                    @php($bonsais = old('bonsais', [[]]))
                    @foreach ($bonsais as $index => $bonsai)
                         @include('registration._bonsai-fields', ['index' => $index, 'bonsai' => $bonsai])
                    @endforeach
                </div>
            </section>

            <div class="flex flex-col items-center justify-between gap-4 rounded-3xl bg-amber-100 p-6 sm:flex-row sm:p-8">
                <p class="max-w-xl text-sm leading-6 text-amber-950">Pastikan foto menampilkan bonsai dengan jelas. Format yang diterima: JPG, PNG, atau WebP, maksimal 50 MB.</p>
                <button type="submit" class="w-full rounded-full bg-amber-500 px-8 py-4 font-semibold text-amber-950 shadow-sm transition hover:bg-amber-400 sm:w-auto">Kirim registrasi</button>
            </div>
        </form>
    </main>

    <template id="bonsai-template">
         @include('registration._bonsai-fields', ['index' => '__INDEX__', 'bonsai' => []])
     </template>
     <script>
         const list = document.querySelector('#bonsai-list');
         const template = document.querySelector('#bonsai-template');
         let index = list.querySelectorAll('[data-bonsai]').length;

        document.querySelector('#add-bonsai').addEventListener('click', () => {
            list.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', index));
            index++;
        });

         list.addEventListener('click', (event) => {
             if (!event.target.matches('[data-remove-bonsai]')) return;
            const cards = list.querySelectorAll('[data-bonsai]');
            if (cards.length === 1) return;
            event.target.closest('[data-bonsai]').remove();
        });

     </script>
</body>
</html>
