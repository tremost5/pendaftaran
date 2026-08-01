<x-landing-layout :title="$brandName">
    @php
        $heroTitleDisplay = trim((string) ($heroTitle ?? 'Pendaftaran Anak TR'));
        $heroSubtitleDisplay = trim((string) ($heroSubtitle ?? 'Talent Regeneration'));
        $heroButtonDisplay = trim((string) ($heroButton ?? '✨ Daftarkan Diri Kamu Yuk'));
        $footerCopyrightDisplay = trim((string) ($footerCopyright ?? '© 2026 DSCMKIDS Online'));
        $footerPoweredDisplay = trim((string) ($footerPowered ?? 'By : Kharis Immanuel Sejahtera'));
        $footerDeveloperDisplay = trim((string) ($footerDeveloper ?? 'Kharis Immanuel Sejahtera'));
    @endphp
    <div
        x-data="registrationForm({
            storageKey: 'dscm-event.registration',
            submitUrl: '{{ route('registrations.store') }}',
            csrfToken: '{{ csrf_token() }}',
        })"
        class="landing-shell flex min-h-dvh flex-col items-center justify-start gap-4 overflow-hidden bg-[#0B1220] px-4 pt-6 pb-8 text-white sm:gap-6 sm:px-6 sm:pt-8 sm:pb-10"
    >
        <div class="pointer-events-none absolute inset-0 -z-20 bg-[radial-gradient(circle_at_top,_rgba(255,211,120,0.08)_0%,_rgba(11,18,32,0.9)_35%,_rgba(11,18,32,1)_100%)]"></div>

        <div class="landing-main-column mx-auto w-full max-w-[960px]">
            <div class="landing-hero text-center">
                <style>
                    /* Hero title gold gradient + glow + shimmer */
                    .hero-title{display:inline-block;font-weight:900;line-height:0.95;letter-spacing:0.01em;background:linear-gradient(180deg,#FFF8D6 0%,#FFE36E 20%,#FFD54A 45%,#FBBF24 70%,#F59E0B 100%);-webkit-background-clip:text;background-clip:text;color:transparent;position:relative;transition:transform .25s ease,filter .25s ease}
                    .hero-title::selection{background:transparent}
                    .hero-title-outline{position:absolute;inset:0;color:transparent;-webkit-text-stroke:1px rgba(255,255,255,.15);pointer-events:none}
                    .hero-title-shadow{text-shadow:0 0 10px rgba(255,200,0,.35),0 0 20px rgba(255,180,0,.30),0 0 40px rgba(255,170,0,.20)}

                    /* subtle moving light behind letters */
                    .hero-title::before{content:"";position:absolute;left:-30%;top:0;width:60%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.18),transparent);transform:skewX(-18deg);filter:blur(8px);opacity:.9;animation:heroShimmer 4.2s linear infinite}
                    @keyframes heroShimmer{0%{left:-40%}50%{left:40%}100%{left:140%}}

                    /* glow pulse */
                    @keyframes heroGlow{0%{filter:drop-shadow(0 0 10px rgba(255,200,0,.18))}50%{filter:drop-shadow(0 0 26px rgba(255,200,0,.28))}100%{filter:drop-shadow(0 0 10px rgba(255,200,0,.18))}}
                    .hero-title.pulse{animation:heroGlow 4s ease-in-out infinite}

                    /* occasional small sparkle */
                    .hero-spark{position:absolute;left:8%;top:-12%;width:8px;height:8px;border-radius:50%;background:radial-gradient(circle,#FFF8D6,#FFD54A);opacity:0;animation:heroSpark 5s linear infinite}
                    @keyframes heroSpark{0%{opacity:0;transform:scale(.6)}45%{opacity:0}60%{opacity:1;transform:scale(1.1)}100%{opacity:0;transform:scale(.6)}}

                    .hero-title:hover{transform:scale(1.01);filter:brightness(1.02)}

                    /* subtitle styling */
                    .hero-subtitle{color:#FFEFB3;font-weight:600;letter-spacing:0.12em;font-size:0.95rem}

                    /* responsive scaling */
                    @media (min-width:1024px){.hero-title{font-size:3.5rem}} /* lg */
                    @media (min-width:640px) and (max-width:1023px){.hero-title{font-size:2.75rem}} /* sm-md */
                    @media (max-width:639px){.hero-title{font-size:1.9rem}} /* mobile */
                </style>

                <h1 class="hero-title hero-title-shadow pulse mx-auto">{{ $heroTitleDisplay }}</h1>
                <div class="mt-3 flex items-center justify-center gap-3">
                    <span class="h-px w-12 bg-white/6 hidden sm:block"></span>
                    <h2 class="hero-subtitle">{{ $heroSubtitleDisplay }}</h2>
                </div>
            </div>

            <div class="landing-hero-poster my-6 flex justify-center">
                <div class="poster-frame landing-poster-frame w-full max-w-[720px] rounded-[16px] overflow-hidden border border-white/6 bg-[#141B2D] shadow-[0_18px_40px_rgba(2,6,23,0.28)]">
                    <img src="{{ $heroImageUrl }}" alt="Poster" class="w-full h-[48vh] object-cover sm:h-[52vh] lg:h-[56vh]" />
                </div>
            </div>

            <div class="text-center mt-1">
                <div class="mt-3 flex justify-center">
                    <style>
                        /* CTA Pill Button - modern gold gradient with shimmer, pulse and chevrons */
                        .cta-pill{position:relative;display:inline-flex;align-items:center;justify-content:center;gap:.75rem;min-height:60px;height:60px;padding:0 1.6rem;border-radius:9999px;border:1px solid rgba(255,215,74,0.18);background:linear-gradient(135deg,#FFD54A,#FBBF24,#F59E0B);color:#1A1A1A;font-weight:800;font-size:0.98rem;line-height:1;cursor:pointer;overflow:visible;transition:transform .3s ease,box-shadow .3s ease}
                        .cta-pill .cta-label{display:inline-block;color:#1A1A1A}
                        .cta-pill .cta-icon{width:20px;height:20px;flex:0 0 auto;display:inline-flex;align-items:center;justify-content:center}

                        /* Glow / layered box-shadow */
                        .cta-pill{box-shadow:0 6px 18px rgba(245,158,11,0.18),0 2px 6px rgba(0,0,0,0.25),0 0 12px rgba(245,158,11,0.06)}
                        @keyframes pulseGlow{0%{box-shadow:0 6px 18px rgba(245,158,11,0.14),0 0 8px rgba(245,158,11,0.04)}50%{box-shadow:0 12px 30px rgba(245,158,11,0.22),0 0 20px rgba(245,158,11,0.08)}100%{box-shadow:0 6px 18px rgba(245,158,11,0.14),0 0 8px rgba(245,158,11,0.04)}}
                        .cta-pill.pulse{animation:pulseGlow 3.8s ease-in-out infinite}

                        /* Shimmer */
                        .cta-pill::after{content:"";position:absolute;top:0;left:-50%;width:40%;height:100%;transform:skewX(-22deg);background:linear-gradient(90deg,rgba(255,255,255,0) 0%,rgba(255,255,255,0.28) 50%,rgba(255,255,255,0) 100%);filter:blur(6px);opacity:.95;animation:ctaShimmer 2.6s linear infinite}
                        @keyframes ctaShimmer{0%{left:-50%}100%{left:160%}}

                        /* Hover and active */
                        .cta-pill:hover{transform:scale(1.05)}
                        .cta-pill:hover::after{filter:blur(8px);opacity:1}
                        .cta-pill:active{transform:scale(.985)}

                        /* Chevrons */
                        .chev{position:absolute;top:50%;transform:translateY(-50%);opacity:.9;font-weight:700;color:rgba(26,26,26,0.95)}
                        .chev.left{left:-3.2rem}
                        .chev.right{right:-3.2rem}
                        @keyframes chevInOutL{0%{transform:translate(-12px,-50%) scale(.9);opacity:.08}30%{transform:translate(-6px,-50%) scale(1);opacity:.16}60%{transform:translate(-2px,-50%) scale(1.05);opacity:.9}100%{transform:translate(-12px,-50%) scale(.9);opacity:.08}}
                        @keyframes chevInOutR{0%{transform:translate(12px,-50%) scale(.9);opacity:.08}30%{transform:translate(6px,-50%) scale(1);opacity:.16}60%{transform:translate(2px,-50%) scale(1.05);opacity:.9}100%{transform:translate(12px,-50%) scale(.9);opacity:.08}}
                        .chev.left{animation:chevInOutL 3.4s ease-in-out infinite}
                        .chev.right{animation:chevInOutR 3.4s ease-in-out infinite}

                        /* Responsive adjustments */
                        @media (max-width:640px){
                            .cta-pill{min-height:56px;height:auto;padding:0.65rem 1rem;font-size:0.95rem}
                            .chev.left{left:-2.4rem}
                            .chev.right{right:-2.4rem}
                        }
                    </style>

                    <button
                        type="button"
                        @click="scrollToSection('registration-form')"
                        onclick="document.getElementById('registration-form')?.scrollIntoView({behavior:'smooth',block:'start'});"
                        class="cta-pill pulse relative z-20 w-full max-w-[320px]"
                        aria-label="{{ $heroButtonDisplay }}"
                    >
                        <span class="chev left">««</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="cta-icon" viewBox="0 0 24 24" fill="none" stroke="#1A1A1A" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden>
                            <path d="M16 11c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3z" />
                            <path d="M6 20v-1a4 4 0 014-4h0" />
                            <path d="M20 8v6" />
                            <path d="M17 11h6" transform="translate(-3 -3)" opacity="0"/>
                        </svg>
                        <span class="cta-label">{{ $heroButtonDisplay }}</span>
                        <span class="chev right">»»</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="landing-main-stack w-full">
            <div class="w-full">
                    <section id="registration-form" class="mt-12">
                        <div class="mx-auto max-w-[760px] rounded-[20px] border border-white/6 bg-[#141B2D] p-5 shadow-[0_18px_50px_rgba(0,0,0,0.22)] sm:p-6">
                            <div class="mb-6 flex items-center justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.35em] text-amber-200">Formulir Pendataan</p>
                                    <h3 class="mt-2 text-2xl font-semibold text-white">Isi Data Anak TR</h3>
                                </div>
                            </div>

                            <div x-cloak x-show="successMessage" x-transition class="mb-6 rounded-lg border border-emerald-300/20 bg-emerald-500/10 p-4 text-emerald-100">
                                <p class="font-semibold">Pendaftaran berhasil!</p>
                                <p class="mt-1 text-sm leading-6">Terima kasih, <span class="font-semibold" x-text="registeredName"></span>. <span x-text="successMessage"></span></p>
                            </div>

                            <form @submit.prevent="submit" class="space-y-4">
                        <div class="grid gap-6">
                            <div>
                                <label for="full_name" class="mb-2 block text-sm font-semibold text-slate-200">Nama Lengkap *</label>
                                <input
                                    id="full_name"
                                    type="text"
                                    x-model="form.full_name"
                                    @input="markTouched('full_name'); validateField('full_name')"
                                    @blur="markTouched('full_name'); validateField('full_name')"
                                    class="block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none transition placeholder:text-slate-500 focus:border-amber-300/40 focus:ring-4 focus:ring-amber-300/10"
                                    placeholder="Nama lengkap"
                                />
                                <p x-show="fieldsTouched.full_name && errors.full_name" x-text="errors.full_name" class="mt-2 text-sm text-rose-300"></p>
                            </div>

                            <div>
                                <label for="nickname" class="mb-2 block text-sm font-semibold text-slate-200">Nama Panggilan *</label>
                                <input
                                    id="nickname"
                                    type="text"
                                    x-model="form.nickname"
                                    @input="markTouched('nickname'); validateField('nickname')"
                                    @blur="markTouched('nickname'); validateField('nickname')"
                                    class="block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none transition placeholder:text-slate-500 focus:border-amber-300/40 focus:ring-4 focus:ring-amber-300/10"
                                    placeholder="Nama panggilan"
                                />
                                <p x-show="fieldsTouched.nickname && errors.nickname" x-text="errors.nickname" class="mt-2 text-sm text-rose-300"></p>
                            </div>
                        </div>

                        <div class="grid gap-6">
                            <div>
                                <label for="date_of_birth" class="mb-2 block text-sm font-semibold text-slate-200">Tanggal Lahir *</label>
                                <input
                                    id="date_of_birth"
                                    type="date"
                                    x-model="form.date_of_birth"
                                    @input="markTouched('date_of_birth'); validateField('date_of_birth')"
                                    @blur="markTouched('date_of_birth'); validateField('date_of_birth')"
                                    class="block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none transition placeholder:text-slate-500 focus:border-amber-300/40 focus:ring-4 focus:ring-amber-300/10"
                                />
                                <p x-show="fieldsTouched.date_of_birth && errors.date_of_birth" x-text="errors.date_of_birth" class="mt-2 text-sm text-rose-300"></p>
                            </div>

                            <div>
                                <label for="school_class" class="mb-2 block text-sm font-semibold text-slate-200">Kelas *</label>
                                <input
                                    id="school_class"
                                    type="text"
                                    x-model="form.school_class"
                                    @input="markTouched('school_class'); validateField('school_class')"
                                    @blur="markTouched('school_class'); validateField('school_class')"
                                    class="block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none transition placeholder:text-slate-500 focus:border-amber-300/40 focus:ring-4 focus:ring-amber-300/10"
                                    placeholder="Kelas"
                                />
                                <p x-show="fieldsTouched.school_class && errors.school_class" x-text="errors.school_class" class="mt-2 text-sm text-rose-300"></p>
                            </div>
                        </div>

                        <div>
                            <label for="home_address" class="mb-2 block text-sm font-semibold text-slate-200">Alamat Rumah *</label>
                            <textarea
                                id="home_address"
                                x-model="form.home_address"
                                @input="markTouched('home_address'); validateField('home_address')"
                                @blur="markTouched('home_address'); validateField('home_address')"
                                class="block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none transition placeholder:text-slate-500 focus:border-amber-300/40 focus:ring-4 focus:ring-amber-300/10"
                                rows="4"
                                placeholder="Alamat rumah"
                            ></textarea>
                            <p x-show="fieldsTouched.home_address && errors.home_address" x-text="errors.home_address" class="mt-2 text-sm text-rose-300"></p>
                        </div>

                        <div>
                            <label for="school_origin" class="mb-2 block text-sm font-semibold text-slate-200">Asal Sekolah *</label>
                            <input
                                id="school_origin"
                                type="text"
                                x-model="form.school_origin"
                                @input="markTouched('school_origin'); validateField('school_origin')"
                                @blur="markTouched('school_origin'); validateField('school_origin')"
                                class="block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none transition placeholder:text-slate-500 focus:border-amber-300/40 focus:ring-4 focus:ring-amber-300/10"
                                placeholder="Asal sekolah"
                            />
                            <p x-show="fieldsTouched.school_origin && errors.school_origin" x-text="errors.school_origin" class="mt-2 text-sm text-rose-300"></p>
                        </div>

                        <div>
                            <label for="whatsapp_number" class="mb-2 block text-sm font-semibold text-slate-200">Nomor HP *</label>
                            <input
                                id="whatsapp_number"
                                type="tel"
                                inputmode="numeric"
                                x-model="form.whatsapp_number"
                                @input="markTouched('whatsapp_number'); validateField('whatsapp_number')"
                                @blur="markTouched('whatsapp_number'); validateField('whatsapp_number')"
                                class="block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white outline-none transition placeholder:text-slate-500 focus:border-amber-300/40 focus:ring-4 focus:ring-amber-300/10"
                                placeholder="6281234567890"
                            />
                            <p class="mt-2 text-xs text-slate-400">Gunakan nomor aktif agar konfirmasi WhatsApp bisa diterima.</p>
                            <p x-show="fieldsTouched.whatsapp_number && errors.whatsapp_number" x-text="errors.whatsapp_number" class="mt-2 text-sm text-rose-300"></p>
                        </div>

                        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                            <p class="mb-4 text-sm font-semibold text-slate-100">Gender *</p>
                            <div class="mt-4 flex flex-wrap gap-3">
                                <label class="flex cursor-pointer items-center gap-3 rounded-full border border-white/10 bg-slate-900/60 px-4 py-3 text-sm text-slate-200 transition hover:bg-white/10">
                                    <input type="radio" name="gender" value="Laki-laki" x-model="form.gender" class="border-white/20 bg-slate-900 text-amber-400 focus:ring-amber-300/20" />
                                    Laki-laki
                                </label>
                                <label class="flex cursor-pointer items-center gap-3 rounded-full border border-white/10 bg-slate-900/60 px-4 py-3 text-sm text-slate-200 transition hover:bg-white/10">
                                    <input type="radio" name="gender" value="Perempuan" x-model="form.gender" class="border-white/20 bg-slate-900 text-amber-400 focus:ring-amber-300/20" />
                                    Perempuan
                                </label>
                            </div>
                            <p x-show="fieldsTouched.gender && errors.gender" x-text="errors.gender" class="mt-2 text-sm text-rose-300"></p>
                        </div>

                        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-5">
                            <p class="mb-4 text-sm font-semibold text-slate-100">Minat Pelayanan *</p>
                            <div class="mt-4 grid gap-3">
                                <label class="flex cursor-pointer items-center gap-3 rounded-full border border-white/10 bg-slate-900/60 px-4 py-3 text-sm text-slate-200 transition hover:bg-white/10">
                                    <input type="checkbox" name="service_interests[]" value="Worship Leader" x-model="form.service_interests" class="border-white/20 bg-slate-900 text-amber-400 focus:ring-amber-300/20" />
                                    Worship Leader
                                </label>
                                <label class="flex cursor-pointer items-center gap-3 rounded-full border border-white/10 bg-slate-900/60 px-4 py-3 text-sm text-slate-200 transition hover:bg-white/10">
                                    <input type="checkbox" name="service_interests[]" value="Singer" x-model="form.service_interests" class="border-white/20 bg-slate-900 text-amber-400 focus:ring-amber-300/20" />
                                    Singer
                                </label>
                                <label class="flex cursor-pointer items-center gap-3 rounded-full border border-white/10 bg-slate-900/60 px-4 py-3 text-sm text-slate-200 transition hover:bg-white/10">
                                    <input type="checkbox" name="service_interests[]" value="Creative Ministry" x-model="form.service_interests" class="border-white/20 bg-slate-900 text-amber-400 focus:ring-amber-300/20" />
                                    Creative Ministry
                                </label>
                                <label class="flex cursor-pointer items-center gap-3 rounded-full border border-white/10 bg-slate-900/60 px-4 py-3 text-sm text-slate-200 transition hover:bg-white/10">
                                    <input type="checkbox" name="service_interests[]" value="Multimedia" x-model="form.service_interests" class="border-white/20 bg-slate-900 text-amber-400 focus:ring-amber-300/20" />
                                    Multimedia
                                </label>
                                <label class="flex cursor-pointer items-center gap-3 rounded-full border border-white/10 bg-slate-900/60 px-4 py-3 text-sm text-slate-200 transition hover:bg-white/10">
                                    <input type="checkbox" name="service_interests[]" value="Musik - Drum" x-model="form.service_interests" class="border-white/20 bg-slate-900 text-amber-400 focus:ring-amber-300/20" />
                                    Musik - Drum
                                </label>
                                <label class="flex cursor-pointer items-center gap-3 rounded-full border border-white/10 bg-slate-900/60 px-4 py-3 text-sm text-slate-200 transition hover:bg-white/10">
                                    <input type="checkbox" name="service_interests[]" value="Musik - Keyboard" x-model="form.service_interests" class="border-white/20 bg-slate-900 text-amber-400 focus:ring-amber-300/20" />
                                    Musik - Keyboard
                                </label>
                                <label class="flex cursor-pointer items-center gap-3 rounded-full border border-white/10 bg-slate-900/60 px-4 py-3 text-sm text-slate-200 transition hover:bg-white/10">
                                    <input type="checkbox" name="service_interests[]" value="Musik - Bass" x-model="form.service_interests" class="border-white/20 bg-slate-900 text-amber-400 focus:ring-amber-300/20" />
                                    Musik - Bass
                                </label>
                                <label class="flex cursor-pointer items-center gap-3 rounded-full border border-white/10 bg-slate-900/60 px-4 py-3 text-sm text-slate-200 transition hover:bg-white/10">
                                    <input type="checkbox" name="service_interests[]" value="Musik - Gitar" x-model="form.service_interests" class="border-white/20 bg-slate-900 text-amber-400 focus:ring-amber-300/20" />
                                    Musik - Gitar
                                </label>
                                <label class="flex cursor-pointer items-center gap-3 rounded-full border border-white/10 bg-slate-900/60 px-4 py-3 text-sm text-slate-200 transition hover:bg-white/10">
                                    <input type="checkbox" name="service_interests[]" value="Usher" x-model="form.service_interests" class="border-white/20 bg-slate-900 text-amber-400 focus:ring-amber-300/20" />
                                    Usher
                                </label>
                            </div>
                            <p x-show="fieldsTouched.service_interests && errors.service_interests" x-text="errors.service_interests" class="mt-2 text-sm text-rose-300"></p>
                        </div>

                        <p x-show="errors.form" x-text="errors.form" class="text-sm text-rose-300"></p>

                        <div class="space-y-4">
                            <button
                                type="submit"
                                :disabled="submitting"
                                class="inline-flex w-full items-center justify-center gap-3 rounded-full border border-amber-200/30 bg-gradient-to-r from-amber-300 via-yellow-400 to-amber-500 px-6 py-4 text-sm font-black uppercase tracking-[0.2em] text-slate-950 shadow-[0_18px_55px_rgba(217,164,31,0.42)] transition disabled:cursor-not-allowed disabled:opacity-70"
                            >
                                <span x-show="!submitting">Kirim Pendaftaran</span>
                                <span x-show="submitting">Memproses...</span>
                            </button>

                            <p class="text-center text-xs text-slate-400">Dengan mendaftar, Anda akan menerima konfirmasi pendaftaran melalui WhatsApp.</p>
                        </div>
                    </form>
                </div>
            </section>

            <section id="participant-list" class="mt-12 transition-all duration-700 ease-out">
                <div class="rounded-[28px] border border-white/6 bg-[#141B2D]/95 p-4 shadow-[0_20px_60px_rgba(0,0,0,0.22)] sm:p-6">
                    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.35em] text-amber-200">Anak yang Sudah Terdaftar</p>
                            <h2 class="mt-2 text-2xl font-semibold text-white sm:text-3xl">Daftar Peserta</h2>
                        </div>
                        <div class="ml-auto">
                            <div class="flex items-center gap-4">
                                <div class="rounded-lg border border-white/6 bg-[#0b1220] p-3 text-center">
                                    <div class="text-2xl text-amber-200">👥</div>
                                    <div class="text-xl font-bold text-white">{{ $totalRegistrations }}</div>
                                    <div class="text-xs text-slate-300">Anak Terdaftar</div>
                                </div>
                            </div>
                        </div>
                    </div>

                        <div class="space-y-4 max-h-[480px] overflow-y-auto pr-2">
                        @forelse($registrations as $registration)
                            <div class="rounded-lg border border-white/6 bg-[#0f1720]/60 p-3 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/6 text-amber-200 text-xl">👤</div>
                                    <div class="flex-1">
                                        <p class="text-base font-semibold text-white">{{ $registration->full_name }}</p>
                                        <p class="text-sm text-slate-300">Kelas {{ $registration->school_class }}</p>
                                        <div class="mt-2 flex flex-wrap items-center gap-2">
                                            @foreach($registration->service_interests ?? [] as $interest)
                                                @php
                                                    $badgeClasses = match(trim($interest)) {
                                                        'Singer' => 'bg-pink-500 text-white border-pink-400',
                                                        'Keyboard' => 'bg-sky-500 text-white border-sky-400',
                                                        'Multimedia' => 'bg-purple-600 text-white border-purple-400',
                                                        'Creative Ministry' => 'bg-orange-500 text-white border-orange-400',
                                                        'Usher' => 'bg-green-500 text-white border-green-400',
                                                        'Worship Leader' => 'bg-amber-500 text-white border-amber-400',
                                                        'Musik - Drum' => 'bg-red-500 text-white border-red-400',
                                                        'Musik - Bass' => 'bg-teal-500 text-white border-teal-400',
                                                        'Musik - Gitar' => 'bg-indigo-600 text-white border-indigo-400',
                                                        default => 'bg-slate-700 text-white border-white/10',
                                                    };
                                                @endphp
                                                <span class="flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.08em] {{ $badgeClasses }}">{{ $interest }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-400">Belum ada peserta terdaftar.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        <footer class="mt-14 w-full">
            <div class="mx-auto max-w-3xl text-center text-xs text-slate-400 space-y-1 py-6">
                <div>{{ $footerCopyrightDisplay }}</div>
                <div>{{ $footerPoweredDisplay }}</div>
                <div>{{ $footerDeveloperDisplay }}</div>
            </div>
        </footer>
    </div>
</x-landing-layout>
