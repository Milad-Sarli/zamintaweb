<script lang="ts">
    import HeadingSmall from '@/components/HeadingSmall.svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import {
        Dialog,
        DialogClose,
        DialogContent,
        DialogDescription,
        DialogFooter,
        DialogHeader,
        DialogTitle,
        DialogTrigger,
    } from '@/components/ui/dialog';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { t } from '@/lib/utils';
    import { Form } from '@inertiajs/svelte';

    let passwordInput: HTMLInputElement | undefined;
</script>

<div class="space-y-6">
    <HeadingSmall title={t('ui.delete_account')} description={t('ui.delete_account_desc')} />
    <div class="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10">
        <div class="relative space-y-0.5 text-red-600 dark:text-red-100">
            <p class="font-medium">{t('ui.warning')}</p>
            <p class="text-sm">{t('ui.warning_undone')}</p>
        </div>
        <Dialog>
            <DialogTrigger>
                <Button variant="destructive">{t('ui.delete_account')}</Button>
            </DialogTrigger>
            <DialogContent>
                <Form
                    method="delete"
                    action={route('profile.destroy')}
                    class="space-y-6"
                    resetOnSuccess
                    onError={(errors) => {
                        if (errors.password) {
                            passwordInput?.focus();
                        }
                    }}
                >
                    {#snippet children({
                        errors,
                        processing,
                        reset,
                        clearErrors,
                    }: {
                        errors: Record<string, string>;
                        processing: boolean;
                        reset: () => void;
                        clearErrors: () => void;
                    })}
                        <DialogHeader class="space-y-3">
                            <DialogTitle>{t('ui.confirm_delete')}</DialogTitle>
                            <DialogDescription>
                                {t('ui.delete_warning_desc')}
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-2">
                            <Label for="password" class="sr-only">{t('ui.password')}</Label>
                            <Input id="password" type="password" name="password" bind:ref={passwordInput} placeholder={t('ui.password')} />
                            <InputError message={errors.password} />
                        </div>

                        <DialogFooter class="gap-2">
                            <DialogClose>
                                <Button
                                    variant="secondary"
                                    onclick={() => {
                                        clearErrors();
                                        reset();
                                    }}>{t('ui.cancel')}</Button
                                >
                            </DialogClose>

                            <Button type="submit" variant="destructive" disabled={processing}>{t('ui.delete_account')}</Button>
                        </DialogFooter>
                    {/snippet}
                </Form>
            </DialogContent>
        </Dialog>
    </div>
</div>
