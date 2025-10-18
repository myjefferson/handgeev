<!-- Rodapé -->
<footer class="footer py-6 px-4 mt-auto">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center mb-4 md:mb-0">
                <a href="{{ route('landing.handgeev') }}">
                    <img class="w-28 opacity-60 grayscale hover:grayscale-0 transition-all" src="{{ asset('assets/images/logo.png') }}" alt="Handgeev Logo">
                </a>
            </div>
            
            <div class="flex space-x-6 mb-4 md:mb-0">
                <a href="{{ route('legal.terms') }}" class="text-slate-400 hover:text-primary-500 transition-colors text-sm">
                    {{ __('footer.links.terms') }}
                </a>
                <a href="{{ route('legal.privacy') }}" class="text-slate-400 hover:text-primary-500 transition-colors text-sm">
                    {{ __('footer.links.privacy') }}
                </a>
                <a href="#" class="text-slate-400 hover:text-primary-500 transition-colors text-sm">
                    {{ __('footer.links.support') }}
                </a>
            </div>
            
            <div class="flex space-x-4">
                {{-- <a href="#" class="social-icon text-slate-400 text-lg hover:text-primary-500 transition-colors" title="{{ __('footer.social.facebook') }}">
                    <i class="fab fa-facebook-f"></i>
                </a> --}}
                {{-- <a href="#" class="social-icon text-slate-400 text-lg hover:text-primary-500 transition-colors" title="{{ __('footer.social.twitter') }}">
                    <i class="fab fa-twitter"></i>
                </a> --}}
                <a href="https://www.instagram.com/hand.geev/" class="social-icon text-slate-400 text-lg hover:text-primary-500 transition-colors" title="{{ __('footer.social.instagram') }}">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.linkedin.com/company/handgeev" class="social-icon text-slate-400 text-lg hover:text-primary-500 transition-colors" title="{{ __('footer.social.linkedin') }}">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>
        </div>
        
        <div class="flex flex-col md:flex-row justify-between items-center border-t border-slate-700 mt-4 pt-4">
            <p class="text-xs text-slate-500 mb-4 md:mb-0">
                {!! __('footer.copyright', ['year' => date('Y')]) !!}
            </p>
            @include('components.switcher.language-switcher')
        </div>
    </div>
</footer>