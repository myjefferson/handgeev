@extends('template.template-site')

@section('title', __('recovery.title'))

@section('content_site')
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .recovery-container {
            backdrop-filter: blur(10px);
            border-radius: 16px;
            overflow: hidden;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #08fff0 0%, #00b3a8 100%);
        }
        .input-field {
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .input-field:focus {
            border-color: #08fff0;
            box-shadow: 0 0 0 3px rgba(8, 255, 240, 0.2);
        }
        .btn-primary {
            background: linear-gradient(135deg, #08fff0 0%, #00b3a8 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #00e6d8 0%, #008078 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(8, 255, 240, 0.3);
        }
        .footer {
            background: rgba(15, 23, 42, 0.95);
            border-top: 1px solid rgba(8, 255, 240, 0.1);
            backdrop-filter: blur(10px);
        }
        .social-icon {
            transition: all 0.3s ease;
        }
        .social-icon:hover {
            color: #08fff0;
            transform: translateY(-3px);
        }
        .logo-text {
            background: linear-gradient(135deg, #08fff0 0%, #00b3a8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .back-link {
            transition: all 0.3s ease;
        }
        .back-link:hover {
            color: #08fff0;
            transform: translateX(-3px);
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <div class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="recovery-container w-full max-w-md bg-slate-800/90 backdrop-blur-sm rounded-xl border border-slate-700 p-8 shadow-2xl">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <img class="w-52" src="{{ asset('assets/images/logo.png') }}" alt="Handgeev Logo">
                </div>
                <h2 class="text-2xl font-bold text-slate-100 mb-2">{{ __('recovery.header.title') }}</h2>
                <p class="text-slate-300">{{ __('recovery.header.subtitle') }}</p>
            </div>

            <!-- Mensagens de status/feedback -->
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-400 bg-green-900/20 p-3 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <form class="space-y-6" action="{{ route('recovery.password.email') }}" method="POST">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-2">{{ __('recovery.form.email') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="{{ __('recovery.icons.email') }} text-slate-500"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                            class="input-field appearance-none relative block bg-slate-700/50 w-full pl-10 pr-10 py-3 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-0 sm:text-sm" 
                            placeholder="{{ __('recovery.form.email_placeholder') }}" value="{{ old('email') }}">
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="btn-primary group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-md">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="{{ __('recovery.icons.paper_plane') }}"></i>
                        </span>
                        {{ __('recovery.form.submit_button') }}
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login.show') }}" class="back-link inline-flex items-center text-sm font-medium text-primary-500 hover:text-primary-400 transition-colors">
                    <i class="{{ __('recovery.icons.arrow_left') }} mr-2"></i>
                    {{ __('recovery.form.back_to_login') }}
                </a>
            </div>
        </div>
    </div>

    @include('components.footer.footer')
    
@endsection