export type TenantSetting = {
    id: number;
    business_name: string;
    address?: string;
    logo_url?: string;
    logo_path?: string;
    base_currency?: string;
    invoice_prefix?: string;
    invoice_next_number?: number;
    default_payment_terms_days?: number;
    timezone?: string;
    created_at: string;
    updated_at: string;
};