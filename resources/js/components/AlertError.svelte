<script lang="ts">
    import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
    import { CircleAlert } from 'lucide-svelte';
    import { t } from '@/lib/utils';

    interface Props {
        errors: string[];
        title?: string;
    }

    let { errors, title = 'ui.something_went_wrong' }: Props = $props();

    const uniqueErrors = $derived([...new Set(errors)]);
</script>

<Alert variant="destructive">
    <CircleAlert class="size-4" />
    <AlertTitle>{t(title)}</AlertTitle>
    <AlertDescription>
        <ul class="list-inside list-disc text-sm">
            {#each uniqueErrors as error, index (index)}
                <li>{error}</li>
            {/each}
        </ul>
    </AlertDescription>
</Alert>
