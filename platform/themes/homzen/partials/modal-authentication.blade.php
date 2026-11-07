<div class="modal fade editorial-auth-modal" id="modalLogin" tabindex="-1" aria-label="{{ __('Sign in to your account') }}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>

            {!! $loginForm->renderForm() !!}
        </div>
    </div>
</div>

@if (RealEstateHelper::isRegisterEnabled())
    <div class="modal modal-lg fade editorial-auth-modal" id="modalRegister" tabindex="-1" aria-label="{{ __('Create an account') }}">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>

                {!! $registerForm->renderForm() !!}
            </div>
        </div>
    </div>
@endif
