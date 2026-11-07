<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        // table => [fk_column, fk_index_name, composite_index_name]
        $tables = [
            're_accounts_translations' => ['re_accounts_id', 'idx_re_accounts_trans_fk', 'idx_re_accounts_trans_fk_lang'],
            're_categories_translations' => ['re_categories_id', 'idx_re_categories_trans_fk', 'idx_re_categories_trans_fk_lang'],
            're_consult_custom_field_options_translations' => ['re_consult_custom_field_options_id', 'idx_re_ccfo_trans_fk', 'idx_re_ccfo_trans_fk_lang'],
            're_consult_custom_fields_translations' => ['re_consult_custom_fields_id', 'idx_re_ccf_trans_fk', 'idx_re_ccf_trans_fk_lang'],
            're_custom_field_options_translations' => ['re_custom_field_options_id', 'idx_re_cfo_trans_fk', 'idx_re_cfo_trans_fk_lang'],
            're_custom_field_values_translations' => ['re_custom_field_values_id', 'idx_re_cfv_trans_fk', 'idx_re_cfv_trans_fk_lang'],
            're_custom_fields_translations' => ['re_custom_fields_id', 'idx_re_cf_trans_fk', 'idx_re_cf_trans_fk_lang'],
            're_facilities_translations' => ['re_facilities_id', 'idx_re_facilities_trans_fk', 'idx_re_facilities_trans_fk_lang'],
            're_features_translations' => ['re_features_id', 'idx_re_features_trans_fk', 'idx_re_features_trans_fk_lang'],
            're_investors_translations' => ['re_investors_id', 'idx_re_investors_trans_fk', 'idx_re_investors_trans_fk_lang'],
            're_packages_translations' => ['re_packages_id', 'idx_re_packages_trans_fk', 'idx_re_packages_trans_fk_lang'],
        ];

        foreach ($tables as $table => $config) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            [$foreignKey, $fkIndex, $compositeIndex] = $config;

            Schema::table($table, function (Blueprint $blueprint) use ($foreignKey, $fkIndex, $compositeIndex) {
                $blueprint->index($foreignKey, $fkIndex);
                $blueprint->index([$foreignKey, 'lang_code'], $compositeIndex);
            });
        }
    }

    public function down(): void
    {
        $tables = [
            're_accounts_translations' => ['idx_re_accounts_trans_fk', 'idx_re_accounts_trans_fk_lang'],
            're_categories_translations' => ['idx_re_categories_trans_fk', 'idx_re_categories_trans_fk_lang'],
            're_consult_custom_field_options_translations' => ['idx_re_ccfo_trans_fk', 'idx_re_ccfo_trans_fk_lang'],
            're_consult_custom_fields_translations' => ['idx_re_ccf_trans_fk', 'idx_re_ccf_trans_fk_lang'],
            're_custom_field_options_translations' => ['idx_re_cfo_trans_fk', 'idx_re_cfo_trans_fk_lang'],
            're_custom_field_values_translations' => ['idx_re_cfv_trans_fk', 'idx_re_cfv_trans_fk_lang'],
            're_custom_fields_translations' => ['idx_re_cf_trans_fk', 'idx_re_cf_trans_fk_lang'],
            're_facilities_translations' => ['idx_re_facilities_trans_fk', 'idx_re_facilities_trans_fk_lang'],
            're_features_translations' => ['idx_re_features_trans_fk', 'idx_re_features_trans_fk_lang'],
            're_investors_translations' => ['idx_re_investors_trans_fk', 'idx_re_investors_trans_fk_lang'],
            're_packages_translations' => ['idx_re_packages_trans_fk', 'idx_re_packages_trans_fk_lang'],
        ];

        foreach ($tables as $table => $indexes) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($indexes) {
                $blueprint->dropIndex($indexes[0]);
                $blueprint->dropIndex($indexes[1]);
            });
        }
    }
};
