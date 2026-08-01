const defaultForm = () => ({
    full_name: '',
    nickname: '',
    date_of_birth: '',
    home_address: '',
    school_origin: '',
    school_class: '',
    gender: '',
    service_interests: [],
    whatsapp_number: '',
});

export function registrationForm(config = {}) {
    return {
        storageKey: config.storageKey || 'dscm-event.registration',
        submitUrl: config.submitUrl,
        csrfToken: config.csrfToken,
        submitting: false,
        submitted: false,
        lastSubmittedAt: 0,
        registeredName: '',
        fieldsTouched: {
            full_name: false,
            nickname: false,
            date_of_birth: false,
            home_address: false,
            school_origin: false,
            school_class: false,
            gender: false,
            service_interests: false,
            whatsapp_number: false,
        },
        form: defaultForm(),
        errors: {},
        successMessage: '',
        showHero: false,
        showForm: false,
        showRegistrations: false,

        init() {
            this.restore();
            window.addEventListener('beforeunload', () => this.persist());

            this.$watch('form.full_name', () => this.persist());
            this.$watch('form.nickname', () => this.persist());
            this.$watch('form.date_of_birth', () => this.persist());
            this.$watch('form.home_address', () => this.persist());
            this.$watch('form.school_origin', () => this.persist());
            this.$watch('form.school_class', () => this.persist());
            this.$watch('form.gender', () => this.persist());
            this.$watch('form.service_interests', () => this.persist());
            this.$watch('form.whatsapp_number', () => this.persist());
        },

        scrollToSection(id) {
            const element = document.getElementById(id);
            if (!element) return;
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },

        keepEditing() {
            this.errors = {};
        },

        openSuccess(message) {
            this.successMessage = message || 'Pendaftaran berhasil.';
        },

        isDirty() {
            return Boolean(
                this.form.full_name ||
                this.form.nickname ||
                this.form.date_of_birth ||
                this.form.home_address ||
                this.form.school_origin ||
                this.form.school_class ||
                this.form.gender ||
                this.form.service_interests.length ||
                this.form.whatsapp_number
            );
        },

        markTouched(field) {
            this.fieldsTouched[field] = true;
        },

        normalizeWhatsapp() {
            let value = this.form.whatsapp_number.replace(/[^\d+]/g, '');
            value = value.replace(/^\+?62/, '62');
            value = value.replace(/^0/, '62');
            value = value.replace(/\D/g, '').slice(0, 15);
            this.form.whatsapp_number = value.startsWith('62') ? value : '62' + value;
        },

        validateField(field) {
            this.errors = {
                ...this.errors,
                [field]: null,
            };

            if (field === 'full_name' && !this.form.full_name.trim()) {
                this.errors.full_name = 'Nama lengkap wajib diisi.';
            }

            if (field === 'nickname' && !this.form.nickname.trim()) {
                this.errors.nickname = 'Nama panggilan wajib diisi.';
            }

            if (field === 'date_of_birth' && !this.form.date_of_birth) {
                this.errors.date_of_birth = 'Tanggal lahir wajib diisi.';
            }

            if (field === 'home_address' && !this.form.home_address.trim()) {
                this.errors.home_address = 'Alamat rumah wajib diisi.';
            }

            if (field === 'school_origin' && !this.form.school_origin.trim()) {
                this.errors.school_origin = 'Asal sekolah wajib diisi.';
            }

            if (field === 'school_class' && !this.form.school_class.trim()) {
                this.errors.school_class = 'Kelas wajib diisi.';
            }

            if (field === 'gender' && !this.form.gender) {
                this.errors.gender = 'Gender wajib dipilih.';
            }

            if (field === 'service_interests' && this.form.service_interests.length === 0) {
                this.errors.service_interests = 'Pilih minimal satu bidang pelayanan.';
            }

            if (field === 'whatsapp_number') {
                this.normalizeWhatsapp();
                if (!this.form.whatsapp_number) {
                    this.errors.whatsapp_number = 'Nomor HP wajib diisi.';
                } else if (!/^62\d{8,13}$/.test(this.form.whatsapp_number)) {
                    this.errors.whatsapp_number = 'Gunakan nomor HP yang valid.';
                }
            }
        },

        validateAll() {
            this.errors = {};
            this.validateField('full_name');
            this.validateField('nickname');
            this.validateField('date_of_birth');
            this.validateField('home_address');
            this.validateField('school_origin');
            this.validateField('school_class');
            this.validateField('gender');
            this.validateField('service_interests');
            this.validateField('whatsapp_number');

            this.fieldsTouched = {
                full_name: true,
                nickname: true,
                date_of_birth: true,
                home_address: true,
                school_origin: true,
                school_class: true,
                gender: true,
                service_interests: true,
                whatsapp_number: true,
            };

            return !this.errors.full_name && !this.errors.nickname && !this.errors.date_of_birth && !this.errors.home_address && !this.errors.school_origin && !this.errors.school_class && !this.errors.gender && !this.errors.service_interests && !this.errors.whatsapp_number;
        },

        async submit() {
            if (this.submitting) {
                return;
            }

            if (!this.validateAll()) {
                return;
            }

            const now = Date.now();
            if (now - this.lastSubmittedAt < 1500) {
                this.errors.form = 'Mohon tunggu sebentar sebelum mengirim ulang.';
                return;
            }

            this.submitting = true;
            this.errors = {};

            try {
                const response = await fetch(this.submitUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                    body: JSON.stringify({
                        full_name: this.form.full_name.trim(),
                        nickname: this.form.nickname.trim(),
                        date_of_birth: this.form.date_of_birth,
                        home_address: this.form.home_address.trim(),
                        school_origin: this.form.school_origin.trim(),
                        school_class: this.form.school_class.trim(),
                        gender: this.form.gender,
                        service_interests: this.form.service_interests,
                        whatsapp_number: this.form.whatsapp_number,
                        website: '',
                    }),
                });

                if (response.status === 422) {
                    const payload = await response.json();
                    this.errors = Object.fromEntries(
                        Object.entries(payload.errors || {}).map(([key, value]) => [key, Array.isArray(value) ? value[0] : value]),
                    );
                    return;
                }

                if (!response.ok) {
                    throw new Error('Request failed');
                }

                const payload = await response.json();
                this.lastSubmittedAt = Date.now();
                this.registeredName = this.form.full_name;
                this.clearStorage();
                this.resetForm();
                this.openSuccess(payload.message || 'Pendaftaran berhasil.');
            } catch (error) {
                this.errors = {
                    form: 'Pendaftaran belum bisa diproses. Silakan coba lagi.',
                };
            } finally {
                this.submitting = false;
            }
        },

        persist() {
            if (typeof window === 'undefined') {
                return;
            }

            window.localStorage.setItem(this.storageKey, JSON.stringify(this.form));
        },

        restore() {
            if (typeof window === 'undefined') {
                return;
            }

            const raw = window.localStorage.getItem(this.storageKey);

            if (!raw) {
                return;
            }

            try {
                const stored = JSON.parse(raw);

                this.form = {
                    ...defaultForm(),
                    full_name: stored.full_name ?? '',
                    nickname: stored.nickname ?? '',
                    date_of_birth: stored.date_of_birth ?? '',
                    home_address: stored.home_address ?? '',
                    school_origin: stored.school_origin ?? '',
                    school_class: stored.school_class ?? '',
                    gender: stored.gender ?? '',
                    service_interests: Array.isArray(stored.service_interests) ? stored.service_interests : [],
                    whatsapp_number: String(stored.whatsapp_number || '').replace(/[^\d]/g, '').slice(0, 15),
                };
            } catch (error) {
                this.clearStorage();
            }
        },

        clearStorage() {
            if (typeof window === 'undefined') {
                return;
            }

            window.localStorage.removeItem(this.storageKey);
        },

        resetForm() {
            this.form = defaultForm();
            this.errors = {};
            this.fieldsTouched = {
                full_name: false,
                nickname: false,
                date_of_birth: false,
                home_address: false,
                school_origin: false,
                school_class: false,
                gender: false,
                service_interests: false,
                whatsapp_number: false,
            };
        },
    };
}
