export class JsonRequestError extends Error {
    constructor(
        message: string,
        public readonly errors: Record<string, string[]> = {},
        public readonly status: number = 500,
    ) {
        super(message);
    }
}

function csrfToken(): string {
    return (
        document
            .querySelector<HTMLMetaElement>('meta[name="csrf-token"]')
            ?.getAttribute('content') ?? ''
    );
}

function isRecord(value: unknown): value is Record<string, unknown> {
    return typeof value === 'object' && value !== null;
}

export async function jsonRequest<T>(
    url: string,
    options: RequestInit = {},
): Promise<T> {
    const headers = new Headers(options.headers);
    headers.set('Accept', 'application/json');

    if (options.body !== undefined) {
        headers.set('Content-Type', 'application/json');
    }

    if ((options.method ?? 'GET').toUpperCase() !== 'GET') {
        headers.set('X-CSRF-TOKEN', csrfToken());
    }

    const response = await fetch(url, {
        ...options,
        credentials: 'same-origin',
        headers,
    });
    const payload: unknown = await response.json().catch(() => null);

    if (!response.ok) {
        const message =
            isRecord(payload) && typeof payload.message === 'string'
                ? payload.message
                : 'Something went wrong. Please try again.';
        const errors =
            isRecord(payload) &&
            isRecord(payload.errors) &&
            Object.values(payload.errors).every(
                (value) =>
                    Array.isArray(value) &&
                    value.every((item) => typeof item === 'string'),
            )
                ? (payload.errors as Record<string, string[]>)
                : {};

        throw new JsonRequestError(message, errors, response.status);
    }

    return payload as T;
}
