export function inView(
    node: Element,
    options: { once?: boolean; rootMargin?: string; threshold?: number } = {}
) {
    let done = false;
    const observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting && !done) {
                node.dispatchEvent(new CustomEvent('inview'));
                if (options.once !== false) {
                    done = true;
                    observer.disconnect();
                }
            }
        },
        { rootMargin: options.rootMargin || '-80px', threshold: options.threshold || 0.1 }
    );
    observer.observe(node);
    return {
        destroy() {
            observer.disconnect();
        },
    };
}
