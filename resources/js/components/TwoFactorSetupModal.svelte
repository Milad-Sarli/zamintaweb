<script lang="ts">
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
    import { InputOTP, InputOTPGroup, InputOTPSlot } from '@/components/ui/input-otp';
    import { Spinner } from '@/components/ui/spinner';
    import { createTwoFactorAuth } from '@/lib/two-factor-auth.svelte';
    import { t } from '@/lib/utils';
    import { Form } from '@inertiajs/svelte';
    import { Check, Copy, ScanLine } from 'lucide-svelte';
    import { tick } from 'svelte';

    interface Props {
        isOpen?: boolean;
        requiresConfirmation: boolean;
        twoFactorEnabled: boolean;
    }

    let { isOpen = $bindable(false), requiresConfirmation, twoFactorEnabled }: Props = $props();

    const twoFactorAuth = createTwoFactorAuth();

    let showVerificationStep = $state(false);
    let code = $state('');
    let copied = $state(false);
    let pinInputContainer = $state<HTMLDivElement>();

    const modalConfig = $derived(
        twoFactorEnabled
            ? {
                  title: t('ui.2fa_enabled_title'),
                  description: t('ui.2fa_enabled_modal_desc'),
                  buttonText: t('ui.close'),
              }
            : showVerificationStep
              ? {
                    title: t('ui.verify_auth_code'),
                    description: t('ui.enter_code_desc'),
                    buttonText: t('ui.continue'),
                }
              : {
                    title: t('ui.enable_2fa_title'),
                    description: t('ui.enable_2fa_modal_desc'),
                    buttonText: t('ui.continue'),
                },
    );

    const handleModalNextStep = async () => {
        if (requiresConfirmation) {
            showVerificationStep = true;

            await tick();
            pinInputContainer?.querySelector('input')?.focus();

            return;
        }

        twoFactorAuth.clearSetupData();
        isOpen = false;
    };

    const resetModalState = () => {
        if (twoFactorEnabled) {
            twoFactorAuth.clearSetupData();
        }

        showVerificationStep = false;
        code = '';
    };

    const copyToClipboard = async (text: string) => {
        try {
            await navigator.clipboard.writeText(text);
            copied = true;
            setTimeout(() => (copied = false), 2000);
        } catch {
            // Clipboard API not available
        }
    };

    $effect(() => {
        if (!isOpen) {
            resetModalState();
            return;
        }

        if (!twoFactorAuth.qrCodeSvg) {
            twoFactorAuth.fetchSetupData();
        }
    });
</script>

<Dialog open={isOpen} onOpenChange={(open) => (isOpen = open)}>
    <DialogContent class="sm:max-w-md">
        <DialogHeader class="flex items-center justify-center">
            <div class="mb-3 w-auto rounded-full border border-border bg-card p-0.5 shadow-sm">
                <div class="relative overflow-hidden rounded-full border border-border bg-muted p-2.5">
                    <div class="absolute inset-0 grid grid-cols-5 opacity-50">
                        {#each Array.from({ length: 5 }, (_, i) => i) as index (index)}
                            <div class="border-r border-border last:border-r-0"></div>
                        {/each}
                    </div>
                    {#if twoFactorEnabled}
                        <Check class="relative z-10 size-10 text-muted-foreground" />
                    {:else}
                        <ScanLine class="relative z-10 size-10 text-muted-foreground" />
                    {/if}
                </div>
            </div>
            <DialogTitle class="text-center">{modalConfig.title}</DialogTitle>
            <DialogDescription class="text-center">{modalConfig.description}</DialogDescription>
        </DialogHeader>

        <div class="flex flex-col items-center space-y-4">
            {#if !twoFactorEnabled}
                {#if !showVerificationStep}
                    {#if twoFactorAuth.qrCodeSvg}
                        <div class="rounded-lg border bg-white p-4">
                            <!-- eslint-disable-next-line svelte/no-at-html-tags -->
                            {@html twoFactorAuth.qrCodeSvg}
                        </div>

                        <div class="w-full space-y-1.5">
                            <div class="text-center text-xs font-medium text-muted-foreground uppercase tracking-wider">{t('ui.setup_key')}</div>
                            <div class="flex items-center justify-between rounded-md border bg-muted px-3 py-2">
                                <code class="text-sm font-mono">{twoFactorAuth.manualSetupKey}</code>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="h-8 w-8 text-muted-foreground hover:text-foreground"
                                    onclick={() => copyToClipboard(twoFactorAuth.manualSetupKey!)}
                                >
                                    {#if copied}
                                        <Check class="size-4" />
                                        <span class="sr-only">{t('ui.copied')}</span>
                                    {:else}
                                        <Copy class="size-4" />
                                        <span class="sr-only">{t('ui.copy')}</span>
                                    {/if}
                                </Button>
                            </div>
                        </div>
                    {:else}
                        <div class="flex h-48 w-full items-center justify-center">
                            <Spinner class="size-8" />
                        </div>
                    {/if}
                {:else}
                    <div bind:this={pinInputContainer} class="flex w-full flex-col items-center justify-center gap-4 py-4">
                        <Form
                            method="post"
                            action={route('two-factor.confirm')}
                            preserveScroll={true}
                            onSuccess={() => {
                                isOpen = false;
                                showVerificationStep = false;
                                code = '';
                            }}
                            onError={() => {
                                pinInputContainer?.querySelector('input')?.focus();
                            }}
                            class="w-full"
                        >
                            {#snippet children({ errors, processing })}
                                <div class="flex flex-col items-center gap-4" dir="ltr">
                                    <InputOTP maxLength={6} bind:value={code} disabled={processing} name="code">
                                        {#snippet children({ cells })}
                                            <InputOTPGroup>
                                                {#each cells.slice(0, 3) as cell (cell)}
                                                    <InputOTPSlot {cell} />
                                                {/each}
                                            </InputOTPGroup>
                                            <InputOTPGroup>
                                                {#each cells.slice(3, 6) as cell (cell)}
                                                    <InputOTPSlot {cell} />
                                                {/each}
                                            </InputOTPGroup>
                                        {/snippet}
                                    </InputOTP>
                                </div>

                                {#if errors.code}
                                    <div class="mt-4 w-full">
                                        <InputError message={errors.code} class="text-center" />
                                    </div>
                                {/if}

                                <Button type="submit" class="mt-4 w-full" disabled={processing || code.length !== 6}>
                                    {t('ui.verify_auth_code')}
                                </Button>
                            {/snippet}
                        </Form>
                        <Button variant="ghost" onclick={() => (showVerificationStep = false)} class="w-full text-sm text-muted-foreground">
                            {t('ui.cancel')}
                        </Button>
                    </div>
                {/if}
            {/if}
        </div>

        {#if !showVerificationStep}
            <div class="flex w-full flex-col gap-2">
                <Button onclick={handleModalNextStep} class="w-full">
                    {modalConfig.buttonText}
                </Button>
            </div>
        {/if}
    </DialogContent>
</Dialog>
