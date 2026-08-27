export type PricingPlanConfig = {
    monthly: number | string;
    setup: number | string;
    allowance: number | string;
};

export type DocuflowPublicConfig = {
    contact: {
        email: string | null;
        phone: string | null;
        whatsapp: string | null;
    };
    pricing: {
        starter: PricingPlanConfig;
        growth: PricingPlanConfig;
        professional: PricingPlanConfig;
        terms: {
            overage: string;
            cancellation: string;
        };
    };
};
