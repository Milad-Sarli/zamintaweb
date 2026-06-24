<script lang="ts">
    let { class: className = '' }: { class?: string } = $props();
</script>

<img
    src="/logo.png"
    alt="Tekmil"
    class={className}
    style="object-fit: contain;"
/>
