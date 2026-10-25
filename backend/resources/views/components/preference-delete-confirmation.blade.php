<dialog
    data-preference-delete-dialog
    aria-labelledby="preference-delete-title"
    aria-describedby="preference-delete-message"
    class="tc-preference-delete-dialog"
>
    <div class="tc-preference-delete-dialog__layout">
        <section class="tc-preference-delete-dialog__panel">
            <div class="flex items-start gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-50 text-red-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.86 1.82 18a2 2 0 001.72 3h16.92a2 2 0 001.72-3L13.7 3.86a2 2 0 00-3.4 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 id="preference-delete-title" class="text-lg font-semibold text-gray-900">
                        {{ __('travel_preferences.delete_question') }}
                    </h2>
                    <p id="preference-delete-message" class="mt-2 text-sm text-gray-600">
                        {{ __('travel_preferences.delete_confirm') }}
                    </p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" data-preference-delete-cancel class="tc-btn-outline px-4 py-2 text-sm font-medium">
                    {{ __('travel_preferences.cancel') }}
                </button>
                <button type="button" data-preference-delete-confirm class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-red-700">
                    {{ __('travel_preferences.confirm_delete') }}
                </button>
            </div>
        </section>
    </div>
</dialog>

<script>
    (() => {
        const dialog = document.querySelector('[data-preference-delete-dialog]');
        if (!dialog) return;

        let pendingForm = null;
        const cancelButton = dialog.querySelector('[data-preference-delete-cancel]');
        const confirmButton = dialog.querySelector('[data-preference-delete-confirm]');

        document.querySelectorAll('[data-preference-delete-form]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                pendingForm = form;
                dialog.showModal();
                cancelButton.focus();
            });
        });

        cancelButton.addEventListener('click', () => dialog.close());
        confirmButton.addEventListener('click', () => {
            const form = pendingForm;
            pendingForm = null;
            dialog.close();
            form?.submit();
        });

        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) dialog.close();
        });
        dialog.addEventListener('close', () => {
            pendingForm = null;
        });
    })();
</script>
