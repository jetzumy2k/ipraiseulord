const VISITOR_KEY = 'visitor_id';

function generateId() {
    if (typeof crypto !== 'undefined' && crypto.randomUUID) {
        return crypto.randomUUID();
    }
    return `v-${Date.now()}-${Math.random().toString(36).slice(2)}`;
}

export function useVisitor() {
    let visitorId = localStorage.getItem(VISITOR_KEY);
    if (!visitorId) {
        visitorId = generateId();
        localStorage.setItem(VISITOR_KEY, visitorId);
    }

    return { visitorId };
}
