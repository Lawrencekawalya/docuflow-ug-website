export type PricingPlanConfig = {
    monthly: number | string | null;
    setup: number | string | null;
    allowance: number | string | null;
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
    };
};
