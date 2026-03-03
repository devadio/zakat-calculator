<?php
/**
 * Plugin Name: Zakat Ultimate
 * Description: Lightweight Ramadan calculator for Zakat al-Maal, Zakat al-Fitr, Fidya, and Kaffarah.
 * Version: 3.2.0
 * Author: Custom Plugin
 * Text Domain: zakat-ultimate
 */

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('zakat_ultimate', 'zscu_render_calculator');
add_shortcode('zakat_ultimate_pro', 'zscu_render_calculator_pro');

function zscu_render_calculator($atts) {
    return zscu_render_calculator_base($atts, false);
}

function zscu_render_calculator_pro($atts) {
    return zscu_render_calculator_base($atts, true);
}

function zscu_render_calculator_base($atts, $show_cta) {
    $id = 'zscu-' . wp_rand(1000, 9999);
    $atts = shortcode_atts(
        array(
            'default' => 'maal',
        ),
        (array) $atts,
        'zakat_ultimate'
    );
    $default_mode = zscu_normalize_default_mode($atts['default']);

    ob_start();
    ?>
    <div id="<?php echo esc_attr($id); ?>" class="zscu-wrap" data-show-cta="<?php echo $show_cta ? '1' : '0'; ?>" data-default-mode="<?php echo esc_attr($default_mode); ?>">
        <style>
            .zscu-wrap{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;max-width:620px;margin:20px auto;background:#fff;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.08);padding:16px;min-height:540px;max-height:640px;display:flex;flex-direction:column;overflow:hidden;border:1px solid #e5e7eb;position:relative}
            .zscu-head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start}
            .zscu-title{margin:0;color:#1a5f3c;font-size:18px;font-weight:800;line-height:1.2}
            .zscu-sub{margin:4px 0 0;color:#4b5563;font-size:13px}
            .zscu-lang{position:relative}
            .zscu-lang-btn{border:1px solid #d1d5db;background:#fff;border-radius:10px;padding:8px 10px;font-size:12px;font-weight:700;display:flex;gap:6px;align-items:center;cursor:pointer}
            .zscu-lang-menu{position:absolute;top:calc(100% + 6px);right:0;background:#fff;border:1px solid #d1d5db;border-radius:10px;box-shadow:0 8px 20px rgba(0,0,0,.1);display:none;max-height:220px;overflow:auto;z-index:5;min-width:170px}
            .zscu-lang-menu.open{display:block}
            .zscu-lang-item{width:100%;background:transparent;border:0;padding:8px 10px;display:flex;justify-content:space-between;cursor:pointer;text-align:left}
            .zscu-lang-item:hover{background:#f9fafb}
            .zscu-content{flex:1;overflow-y:auto;padding-right:6px;margin-right:-6px}
            .zscu-content::-webkit-scrollbar{width:6px}.zscu-content::-webkit-scrollbar-thumb{background:#c1c1c1;border-radius:3px}
            .zscu-modes{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin:12px 0 10px}
            .zscu-choose{margin:6px 0 0;font-size:13px;font-weight:700;color:#374151}
            .zscu-mode{border:2px solid #e5e7eb;background:#fff;border-radius:10px;padding:10px 8px;font-size:12px;font-weight:700;cursor:pointer}
            .zscu-mode.active{border-color:#1a5f3c;background:#f0fdf4;color:#14532d}
            .zscu-panel{display:none}.zscu-panel.active{display:block}
            .zscu-pt{margin:0 0 8px;font-size:17px;font-weight:800;color:#1f2937}
            .zscu-note{margin:0 0 12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:10px;color:#334155;font-size:12px;line-height:1.5;white-space:pre-line}
            .zscu-note-green{background:#f0fdf4;border-color:#bbf7d0}
            .zscu-checks{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:10px}
            .zscu-check{border:1px solid #d1d5db;border-radius:8px;padding:8px;display:flex;gap:8px;align-items:center;font-size:13px;font-weight:600}
            .zscu-field{margin-bottom:10px}.zscu-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin-bottom:10px}
            .zscu-label{display:block;margin-bottom:5px;font-size:12px;font-weight:700;color:#374151}
            .zscu-input,.zscu-select{width:100%;border:2px solid #e5e7eb;border-radius:8px;padding:10px 12px;font-size:14px;box-sizing:border-box}
            .zscu-input:focus,.zscu-select:focus,.zscu-mode:focus-visible,.zscu-btn:focus-visible,.zscu-help:focus-visible,.zscu-lang-btn:focus-visible{outline:2px solid #1a5f3c;outline-offset:2px}
            .zscu-choices{display:grid;gap:8px;margin-bottom:10px}
            .zscu-choice{border:1px solid #d1d5db;border-radius:9px;padding:10px;display:grid;grid-template-columns:1fr auto;align-items:center;gap:8px}
            .zscu-choice-main{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600}
            .zscu-help{border:0;width:20px;height:20px;border-radius:999px;background:#e5e7eb;font-weight:800;cursor:pointer}
            .zscu-err{border:1px solid #fecaca;background:#fef2f2;color:#991b1b;border-radius:9px;padding:8px 10px;font-size:12px;margin-bottom:10px}
            .zscu-err[hidden]{display:none}
            .zscu-actions{display:flex;gap:8px;border-top:1px solid #e5e7eb;padding-top:10px;margin-top:6px}
            .zscu-btn{flex:1;border:0;border-radius:8px;padding:11px 12px;font-size:14px;font-weight:700;cursor:pointer}
            .zscu-btn-primary{color:#fff;background:linear-gradient(135deg,#1a5f3c,#2e8b57)}
            .zscu-btn-primary.is-disabled{opacity:.55;cursor:not-allowed}
            .zscu-btn-secondary{background:#f3f4f6;color:#374151}
            .zscu-input-error{border-color:#fca5a5 !important;background:#fff1f2}
            .zscu-select-error{border-color:#fca5a5 !important;background:#fff1f2}
            .zscu-check-error{border-color:#fca5a5 !important;background:#fff1f2}
            .zscu-choice-error{border-color:#fca5a5 !important;background:#fff1f2}
            .zscu-result-wrap{margin-top:12px}.zscu-result-wrap[hidden]{display:none}
            .zscu-rt{margin:0 0 8px;font-size:16px;font-weight:800}
            .zscu-result{border:1px solid #e5e7eb;border-radius:12px;padding:12px;background:#fff}
            .zscu-status{display:inline-flex;border-radius:999px;padding:5px 9px;font-size:12px;font-weight:700}
            .zscu-status.ok{background:#dcfce7;color:#166534}.zscu-status.bad{background:#fee2e2;color:#991b1b}.zscu-status.info{background:#dbeafe;color:#1e3a8a}
            .zscu-result-head{display:flex;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:8px}
            .zscu-amount-inline{font-size:22px;font-weight:900;color:#1a5f3c;line-height:1}
            .zscu-amount-inline-label{font-size:13px;color:#374151;font-weight:700}
            .zscu-rows{border-top:1px solid #e5e7eb;padding-top:8px}
            .zscu-row{display:flex;justify-content:space-between;gap:8px;padding:4px 0;font-size:12px}.zscu-row strong{color:#111827}
            .zscu-summary{margin-top:8px;border-top:1px dashed #d1d5db;padding-top:8px;font-size:12px;font-weight:600}
            .zscu-cta{margin-top:10px;background:transparent;color:#1a5f3c;border-top:1px solid #bbf7d0;padding:10px 0 0;font-size:14px;font-weight:800;text-align:center;line-height:1.4}
            .zscu-modal-bg{position:absolute;inset:0;background:rgba(0,0,0,.48);display:none;align-items:center;justify-content:center;padding:14px;z-index:9}
            .zscu-modal-bg.open{display:flex}
            .zscu-modal{max-width:430px;background:#fff;border-radius:12px;padding:14px}
            .zscu-modal h4{margin:0 0 6px}.zscu-modal p{margin:0;font-size:13px;line-height:1.55;color:#374151}
            .zscu-modal-actions{text-align:right;margin-top:10px}
            .zscu-modal-close{border:0;border-radius:8px;background:#111827;color:#fff;padding:8px 11px;font-size:12px;font-weight:700;cursor:pointer}
            .zscu-rtl{direction:rtl}
            .zscu-rtl .zscu-lang-item{text-align:right}
            .zscu-rtl .zscu-choice-main{flex-direction:row-reverse}
            .zscu-rtl .zscu-row{flex-direction:row-reverse}
            @media (max-width:480px){.zscu-wrap{margin:10px;border-radius:12px;min-height:520px;max-height:620px;padding:14px}.zscu-modes,.zscu-checks,.zscu-grid{grid-template-columns:1fr}.zscu-amount-inline{font-size:20px}}
        </style>

        <div class="zscu-head">
            <div>
                <h2 class="zscu-title" data-t="app_title">Zakat Calculator</h2>
            </div>
            <div class="zscu-lang">
                <button type="button" class="zscu-lang-btn" data-r="lang-toggle" aria-expanded="false"><span data-r="lang-flag">🇺🇸</span><span data-r="lang-code">EN</span><span>▼</span></button>
                <div class="zscu-lang-menu" data-r="lang-menu">
                    <button class="zscu-lang-item" type="button" data-lang="en" data-flag="🇺🇸" data-code="EN" data-rtl="0"><span>🇺🇸 English</span><strong>EN</strong></button>
                    <button class="zscu-lang-item" type="button" data-lang="ar" data-flag="🇸🇦" data-code="AR" data-rtl="1"><span>🇸🇦 العربية</span><strong>AR</strong></button>
                    <button class="zscu-lang-item" type="button" data-lang="tr" data-flag="🇹🇷" data-code="TR" data-rtl="0"><span>🇹🇷 Türkçe</span><strong>TR</strong></button>
                    <button class="zscu-lang-item" type="button" data-lang="ur" data-flag="🇵🇰" data-code="UR" data-rtl="1"><span>🇵🇰 اردو</span><strong>UR</strong></button>
                    <button class="zscu-lang-item" type="button" data-lang="id" data-flag="🇮🇩" data-code="ID" data-rtl="0"><span>🇮🇩 Bahasa Indonesia</span><strong>ID</strong></button>
                    <button class="zscu-lang-item" type="button" data-lang="de" data-flag="🇩🇪" data-code="DE" data-rtl="0"><span>🇩🇪 Deutsch</span><strong>DE</strong></button>
                    <button class="zscu-lang-item" type="button" data-lang="es" data-flag="🇪🇸" data-code="ES" data-rtl="0"><span>🇪🇸 Español</span><strong>ES</strong></button>
                    <button class="zscu-lang-item" type="button" data-lang="fr" data-flag="🇫🇷" data-code="FR" data-rtl="0"><span>🇫🇷 Français</span><strong>FR</strong></button>
                </div>
            </div>
        </div>

        <div class="zscu-content">
            <p class="zscu-choose" data-t="please_select">please select:</p>
            <div class="zscu-modes">
                <button type="button" class="zscu-mode active" data-mode="maal" data-t="mode_maal">Zakat al-Maal</button>
                <button type="button" class="zscu-mode" data-mode="fitr" data-t="mode_fitr">Zakat al-Fitr</button>
                <button type="button" class="zscu-mode" data-mode="fk" data-t="mode_fk">Fidya / Kaffarah</button>
            </div>

            <section class="zscu-panel active" data-panel="maal">
                <h3 class="zscu-pt" data-t="maal_title">Zakat al-Maal</h3>
                <p class="zscu-note" data-t="maal_intro">Zakat al-Maal is 2.5% (0.025) of total zakatable wealth after one lunar year (hawl), if it reaches nisab. You must reach either:
Gold nisab = 85 grams of gold
Silver nisab = 595 grams of silver
In modern calculators, some scholars use silver nisab (it benefits the poor more), while others use gold nisab.</p>

                <div class="zscu-checks">
                    <label class="zscu-check"><input type="checkbox" data-f="asset_cash" checked><span data-t="asset_cash">Cash</span></label>
                    <label class="zscu-check"><input type="checkbox" data-f="asset_gold"><span data-t="asset_gold">Gold</span></label>
                    <label class="zscu-check"><input type="checkbox" data-f="asset_silver"><span data-t="asset_silver">Silver</span></label>
                </div>

                <div class="zscu-field" data-sec="cash">
                    <label class="zscu-label" data-t="cash_amount">Cash amount (money)</label>
                    <input class="zscu-input" type="number" min="0" step="0.01" data-f="cash">
                </div>

                <div class="zscu-grid" data-sec="gold" style="display:none">
                    <div>
                        <label class="zscu-label" data-t="gold_price">Gold price per gram (money)</label>
                        <input class="zscu-input" type="number" min="0" step="0.01" data-f="gp">
                    </div>
                    <div>
                        <label class="zscu-label" data-t="gold_weight">Gold weight (grams)</label>
                        <input class="zscu-input" type="number" min="0" step="0.01" data-f="gw">
                    </div>
                </div>

                <div class="zscu-grid" data-sec="silver" style="display:none">
                    <div>
                        <label class="zscu-label" data-t="silver_price">Silver price per gram (money)</label>
                        <input class="zscu-input" type="number" min="0" step="0.01" data-f="sp">
                    </div>
                    <div>
                        <label class="zscu-label" data-t="silver_weight">Silver weight (grams)</label>
                        <input class="zscu-input" type="number" min="0" step="0.01" data-f="sw">
                    </div>
                </div>

                <div class="zscu-field">
                    <label class="zscu-label" data-t="nisab_label">Nisab standard</label>
                    <select class="zscu-select" data-f="nisab">
                        <option value="gold" data-t="nisab_gold">Gold (85g)</option>
                        <option value="silver" data-t="nisab_silver">Silver (595g)</option>
                    </select>
                </div>
            </section>

            <section class="zscu-panel" data-panel="fitr">
                <h3 class="zscu-pt" data-t="fitr_title">Zakat al-Fitr</h3>
                <p class="zscu-note" data-t="fitr_intro">Zakat al-Fitr is one sa'a (about 2.5-3 kg staple food or cash equivalent) per person, estimated in the US in 2026 at $10-$15 per person. If a family head pays before Eid prayer for a household of 5 people including children, the total expected Zakat al-Fitr contribution is $50-$75.</p>

                <div class="zscu-grid">
                    <div>
                        <label class="zscu-label" data-t="fitr_persons">Number of persons</label>
                        <input class="zscu-input" type="number" min="1" step="1" data-f="fp">
                    </div>
                    <div>
                        <label class="zscu-label" data-t="fitr_amount">Average zakat per person (money)</label>
                        <input class="zscu-input" type="number" min="0" step="0.01" data-f="fa">
                    </div>
                </div>
            </section>
            <section class="zscu-panel" data-panel="fk">
                <h3 class="zscu-pt" data-t="fk_title">Fidya / Kaffarah</h3>
                <div class="zscu-choices">
                    <label class="zscu-choice">
                        <span class="zscu-choice-main"><input type="radio" name="<?php echo esc_attr($id); ?>-fk" data-f="fkm" value="fidya"><span data-t="fidya_label">Fidya (sick/elderly)</span></span>
                        <button class="zscu-help" type="button" data-help="fidya">?</button>
                    </label>
                    <label class="zscu-choice">
                        <span class="zscu-choice-main"><input type="radio" name="<?php echo esc_attr($id); ?>-fk" data-f="fkm" value="kaffarah"><span data-t="kaffarah_label">Kaffarah (Intentional fast-break)</span></span>
                        <button class="zscu-help" type="button" data-help="kaffarah">?</button>
                    </label>
                </div>

                <p class="zscu-note zscu-note-green" data-info="fidya" style="display:none" data-t="fidya_intro">Fidya is a day's meals (ta'am miskeen) estimated in the US in 2026 at about $12-$15 per day. If someone misses all 30 days of Ramadan, the expected Fidya contribution is about $360-$450.</p>
                <p class="zscu-note zscu-note-green" data-info="kaffarah" style="display:none" data-t="kaffarah_intro">Kaffarah is feeding 60 poor people (ta'am 60 miskeen). In US-based estimates for 2026, it is often around $300-$600 per day and should be completed before makeup fasts (qada).</p>

                <div class="zscu-grid">
                    <div>
                        <label class="zscu-label" data-t="missed_days">Missed days</label>
                        <input class="zscu-input" type="number" min="1" step="1" data-f="fd">
                    </div>
                    <div>
                        <label class="zscu-label" data-t="amount_day">Estimated amount per day (money)</label>
                        <input class="zscu-input" type="number" min="0" step="0.01" data-f="fda">
                    </div>
                </div>

                <div class="zscu-field">
                    <label class="zscu-label" data-t="persons">Number of persons</label>
                    <input class="zscu-input" type="number" min="1" step="1" data-f="fkp">
                </div>
            </section>

            <div class="zscu-err" data-r="err" hidden></div>

            <div class="zscu-actions">
                <button type="button" class="zscu-btn zscu-btn-primary" data-r="submit" data-t="btn_calc">Calculate</button>
                <button type="button" class="zscu-btn zscu-btn-secondary" data-r="reset" data-t="btn_reset">Reset</button>
            </div>

            <section class="zscu-result-wrap" data-r="result-wrap" hidden>
                <h3 class="zscu-rt" data-t="result_title">Your Result</h3>
                <div data-r="result"></div>
            </section>
        </div>

        <div class="zscu-modal-bg" data-r="modal-bg" aria-hidden="true">
            <div class="zscu-modal" role="dialog" aria-modal="true">
                <h4 data-r="modal-title"></h4>
                <p data-r="modal-body"></p>
                <div class="zscu-modal-actions"><button type="button" class="zscu-modal-close" data-r="modal-close" data-t="btn_close">Close</button></div>
            </div>
        </div>

        <script>
            (function(){
                var root=document.getElementById('<?php echo esc_js($id); ?>');
                if(!root){return;}
                var showCta=root.getAttribute('data-show-cta')==='1';
                var initialMode=(root.getAttribute('data-default-mode')||'maal').toLowerCase();
                if(['maal','fitr','fk'].indexOf(initialMode)===-1){initialMode='maal';}
                var state={lang:'en',mode:'maal',last:null};

                var t={
                    en:{
                        app_title:'Zakat Calculator',app_sub:'',
                        please_select:'please select:',
                        mode_maal:'Zakat al-Maal',mode_fitr:'Zakat al-Fitr',mode_fk:'Fidya / Kaffarah',
                        maal_title:'Zakat al-Maal',fitr_title:'Zakat al-Fitr',fk_title:'Fidya / Kaffarah',
                        maal_intro:"Zakat al-Maal is 2.5% (0.025) of total zakatable wealth after one lunar year (hawl), if it reaches nisab. You must reach either:\nGold nisab = 85 grams of gold\nSilver nisab = 595 grams of silver\nIn modern calculators, some scholars use silver nisab (it benefits the poor more), while others use gold nisab.",
                        asset_cash:'Cash',asset_gold:'Gold',asset_silver:'Silver',cash_amount:'Cash amount (money)',gold_price:'Gold price per gram (money)',gold_weight:'Gold weight (grams)',silver_price:'Silver price per gram (money)',silver_weight:'Silver weight (grams)',
                        nisab_label:'Nisab standard',nisab_gold:'Gold (85g)',nisab_silver:'Silver (595g)',
                        fitr_intro:"Zakat al-Fitr is one sa'a (about 2.5-3 kg staple food or cash equivalent) per person, estimated in the US in 2026 at $10-$15 per person. If a family head pays before Eid prayer for a household of 5 people including children, the total expected Zakat al-Fitr contribution is $50-$75.",
                        fitr_persons:'Number of persons',fitr_amount:'Average zakat per person (money)',
                        fidya_label:'Fidya (sick/elderly)',kaffarah_label:'Kaffarah (Intentional fast-break)',
                        fidya_intro:"Fidya is a day's meals (ta'am miskeen) estimated in the US in 2026 at about $12-$15 per day. If someone misses all 30 days of Ramadan, the expected Fidya contribution is about $360-$450.",
                        kaffarah_intro:"Kaffarah is feeding 60 poor people (ta'am 60 miskeen). In US-based estimates for 2026, it is often around $300-$600 per day and should be completed before makeup fasts (qada).",
                        missed_days:'Missed days',amount_day:'Estimated amount per day (money)',persons:'Number of persons',
                        btn_calc:'Calculate',btn_reset:'Reset',btn_close:'Close',result_title:'Your Result',
                        status_ok:'Eligible',status_bad:'Below Nisab',status_info:'Calculated',
                        due_zakat:'Zakat due',due_fitr:'Zakat al-Fitr total',due_fidya:'Fidya total',due_kaffarah:'Kaffarah total',
                        row_total:'Total wealth',row_nisab:'Nisab value',row_nisab_std:'Nisab standard',row_cash:'Cash value',row_gold:'Gold value',row_silver:'Silver value',row_mode:'Mode',row_amount_person:'Amount per person',row_amount_day:'Amount per day',
                        summary:'Formula',std_gold:'Gold (85g)',std_silver:'Silver (595g)',nisab_na:'Not checked',
                        e_asset:'Select at least one asset for Zakat al-Maal.',e_cash:'Enter a valid cash amount.',e_gold:'Enter valid gold price and gold weight.',e_silver:'Enter valid silver price and silver weight.',e_nisab_gold:'To use Gold nisab, select Gold and enter a valid gold price.',e_nisab_silver:'To use Silver nisab, select Silver and enter a valid silver price.',
                        e_fp:'Enter a valid number of persons for Zakat al-Fitr.',e_fa:'Enter a valid average zakat amount per person.',e_fkm:'Select Fidya or Kaffarah.',e_fd:'Enter valid missed days.',e_fda:'Enter a valid estimated amount per day.',e_fkp:'Enter a valid number of persons.',
                        h_fidya_t:'Fidya (sick/elderly)',h_fidya_b:'Fidya: Paid by elderly, chronically ill, pregnant/breastfeeding women unable to fast later; one meal per missed day.',
                        h_kaff_t:'Kaffarah (Intentional fast-break)',h_kaff_b:'Kaffarah: Due for breaking a fast knowingly without valid reason (e.g., no travel/illness); must precede qada (makeup fasts) in sequence.',
                        cta_maal:'Give Your Zakat to Feed Refugee Children in Need',cta_fitr:'Give Your Zakat al-Fitr to Feed Refugee Children in Need',cta_fidya:'Give Your Fidya to Feed Refugee Children in Need',cta_kaff:'Give Your Kaffarah to Feed Refugee Children in Need'
                    },
                    ar:{
                        app_title:'حاسبة الزكاة',app_sub:'',please_select:'يرجى الاختيار:',
                        mode_maal:'زكاة المال',mode_fitr:'زكاة الفطر',mode_fk:'الفدية / الكفارة',
                        maal_title:'زكاة المال',fitr_title:'زكاة الفطر',fk_title:'الفدية / الكفارة',
                        maal_intro:"زكاة المال هي 2.5% (0.025) من إجمالي المال الخاضع للزكاة بعد مرور حول قمري كامل، إذا بلغ النصاب. يجب أن يبلغ المال أحد النصابين:\nنصاب الذهب = 85 غراماً من الذهب\nنصاب الفضة = 595 غراماً من الفضة\nفي الحاسبات الحديثة، يعتمد بعض العلماء نصاب الفضة لأنه أنفع للفقراء، بينما يعتمد آخرون نصاب الذهب.",
                        asset_cash:'نقد',asset_gold:'ذهب',asset_silver:'فضة',cash_amount:'المبلغ النقدي (مال)',gold_price:'سعر غرام الذهب (مال)',gold_weight:'وزن الذهب (غرام)',silver_price:'سعر غرام الفضة (مال)',silver_weight:'وزن الفضة (غرام)',
                        nisab_label:'معيار النصاب',nisab_gold:'الذهب (85غ)',nisab_silver:'الفضة (595غ)',
                        fitr_intro:"زكاة الفطر هي صاع واحد (حوالي 2.5-3 كغ من القوت أو ما يعادله نقداً) عن كل شخص، وتقدّر في الولايات المتحدة لعام 2026 بنحو 10-15 دولاراً للشخص. إذا دفع رب الأسرة قبل صلاة العيد عن أسرة من 5 أشخاص بمن فيهم الأطفال، فالمجموع المتوقع 50-75 دولاراً.",
                        fitr_persons:'عدد الأشخاص',fitr_amount:'متوسط الزكاة لكل شخص (مال)',
                        fidya_label:'الفدية (مريض/كبير سن)',kaffarah_label:'الكفارة (الإفطار المتعمد)',
                        fidya_intro:"الفدية هي إطعام مسكين عن كل يوم، وتُقدّر في الولايات المتحدة لعام 2026 بنحو 12-15 دولاراً لليوم. ومن فاته صيام الشهر كاملاً (30 يوماً) فالمتوقع 360-450 دولاراً.",
                        kaffarah_intro:"الكفارة هي إطعام 60 مسكيناً. وتقدَّر في الولايات المتحدة لعام 2026 غالباً بين 300-600 دولار لليوم، وتُقدَّم قبل قضاء الصوم.",
                        missed_days:'الأيام الفائتة',amount_day:'المبلغ التقديري لكل يوم (مال)',persons:'عدد الأشخاص',
                        btn_calc:'احسب',btn_reset:'إعادة ضبط',btn_close:'إغلاق',result_title:'النتيجة',
                        status_ok:'مستحق',status_bad:'أقل من النصاب',status_info:'تم الحساب',
                        due_zakat:'الزكاة المستحقة',due_fitr:'إجمالي زكاة الفطر',due_fidya:'إجمالي الفدية',due_kaffarah:'إجمالي الكفارة',
                        row_total:'إجمالي المال',row_nisab:'قيمة النصاب',row_nisab_std:'معيار النصاب',row_cash:'القيمة النقدية',row_gold:'قيمة الذهب',row_silver:'قيمة الفضة',row_mode:'النوع',row_amount_person:'المبلغ لكل شخص',row_amount_day:'المبلغ لكل يوم',
                        summary:'المعادلة',std_gold:'الذهب (85غ)',std_silver:'الفضة (595غ)',nisab_na:'لم يتم التحقق',
                        e_asset:'يرجى اختيار أصل واحد على الأقل لزكاة المال.',e_cash:'أدخل مبلغاً نقدياً صحيحاً.',e_gold:'أدخل سعراً ووزناً صحيحين للذهب.',e_silver:'أدخل سعراً ووزناً صحيحين للفضة.',e_nisab_gold:'لاستخدام نصاب الذهب، اختر الذهب وأدخل سعر ذهب صحيحاً.',e_nisab_silver:'لاستخدام نصاب الفضة، اختر الفضة وأدخل سعر فضة صحيحاً.',
                        e_fp:'أدخل عدداً صحيحاً للأشخاص في زكاة الفطر.',e_fa:'أدخل متوسط زكاة صحيحاً لكل شخص.',e_fkm:'اختر الفدية أو الكفارة.',e_fd:'أدخل عدد أيام فائتة صحيحاً.',e_fda:'أدخل مبلغاً يومياً صحيحاً.',e_fkp:'أدخل عدداً صحيحاً للأشخاص.',
                        h_fidya_t:'الفدية (مريض/كبير سن)',h_fidya_b:'الفدية: على الكبير في السن أو المريض المزمن أو الحامل/المرضع إذا تعذر القضاء لاحقاً، وهي إطعام مسكين عن كل يوم.',
                        h_kaff_t:'الكفارة (الإفطار المتعمد)',h_kaff_b:'الكفارة: تلزم من أفطر عمداً بلا عذر شرعي (مثل السفر أو المرض)، وتكون قبل قضاء الصيام بالترتيب.',
                        cta_maal:'أخرج زكاتك لإطعام أطفال اللاجئين المحتاجين',cta_fitr:'أخرج زكاة الفطر لإطعام أطفال اللاجئين المحتاجين',cta_fidya:'أخرج فديتك لإطعام أطفال اللاجئين المحتاجين',cta_kaff:'أخرج كفارتك لإطعام أطفال اللاجئين المحتاجين'
                    },
                    ur:{
                        app_title:'زکوٰۃ کیلکولیٹر',app_sub:'',please_select:'براہ کرم منتخب کریں:',
                        mode_maal:'زکوٰۃ المال',mode_fitr:'زکوٰۃ الفطر',mode_fk:'فدیہ / کفارہ',
                        maal_title:'زکوٰۃ المال',fitr_title:'زکوٰۃ الفطر',fk_title:'فدیہ / کفارہ',
                        maal_intro:"زکوٰۃ المال کل قابلِ زکوٰۃ مال کا 2.5% (0.025) ہے، بشرطیکہ ایک قمری سال (حول) گزر چکا ہو اور مال نصاب کو پہنچ جائے۔ نصاب کے دو معیار ہیں:\nسونے کا نصاب = 85 گرام سونا\nچاندی کا نصاب = 595 گرام چاندی\nجدید کیلکولیٹرز میں بعض علماء چاندی کے نصاب کو ترجیح دیتے ہیں (فقراء کے لیے زیادہ فائدہ مند)، جبکہ بعض سونے کے نصاب کو اختیار کرتے ہیں۔",
                        asset_cash:'نقدی',asset_gold:'سونا',asset_silver:'چاندی',cash_amount:'نقدی رقم (مال)',gold_price:'سونے کی قیمت فی گرام (مال)',gold_weight:'سونے کا وزن (گرام)',silver_price:'چاندی کی قیمت فی گرام (مال)',silver_weight:'چاندی کا وزن (گرام)',
                        nisab_label:'نصاب کا معیار',nisab_gold:'سونا (85 گرام)',nisab_silver:'چاندی (595 گرام)',
                        fitr_intro:"زکوٰۃ الفطر ہر فرد کے لیے ایک صاع (تقریباً 2.5-3 کلو بنیادی غذا یا اس کی نقد قیمت) ہے۔ امریکا میں 2026 کے اندازے کے مطابق فی فرد تقریباً $10-$15 ہے۔ اگر گھر کا سربراہ عید کی نماز سے پہلے 5 افراد (بچوں سمیت) کی طرف سے ادا کرے تو متوقع کل $50-$75 بنتا ہے۔",
                        fitr_persons:'افراد کی تعداد',fitr_amount:'فی فرد اوسط زکوٰۃ (مال)',
                        fidya_label:'فدیہ (بیمار/معمر)',kaffarah_label:'کفارہ (جان بوجھ کر روزہ توڑنا)',
                        fidya_intro:"فدیہ ایک دن کے بدلے ایک مسکین کا کھانا ہے، جس کا امریکا 2026 میں اندازہ تقریباً $12-$15 فی دن ہے۔ اگر 30 دن پورے رہ جائیں تو متوقع فدیہ $360-$450 بنتا ہے۔",
                        kaffarah_intro:"کفارہ 60 مسکینوں کو کھانا کھلانا ہے۔ امریکا 2026 کے اندازے کے مطابق یہ عموماً $300-$600 فی دن کے قریب ہوتا ہے، اور قضا روزوں سے پہلے ادا کیا جاتا ہے۔",
                        missed_days:'چھوٹے ہوئے دن',amount_day:'فی دن تخمینی رقم (مال)',persons:'افراد کی تعداد',
                        btn_calc:'حساب کریں',btn_reset:'ری سیٹ',btn_close:'بند کریں',result_title:'نتیجہ',
                        status_ok:'لازمی',status_bad:'نصاب سے کم',status_info:'حساب مکمل',
                        due_zakat:'واجب زکوٰۃ',due_fitr:'زکوٰۃ الفطر کل',due_fidya:'فدیہ کل',due_kaffarah:'کفارہ کل',
                        row_total:'کل مال',row_nisab:'نصاب کی قیمت',row_nisab_std:'نصاب کا معیار',row_cash:'نقدی قیمت',row_gold:'سونے کی قیمت',row_silver:'چاندی کی قیمت',row_mode:'قسم',row_amount_person:'فی فرد رقم',row_amount_day:'فی دن رقم',
                        summary:'فارمولا',std_gold:'سونا (85 گرام)',std_silver:'چاندی (595 گرام)',nisab_na:'چیک نہیں کیا گیا',
                        e_asset:'زکوٰۃ المال کے لیے کم از کم ایک اثاثہ منتخب کریں۔',e_cash:'درست نقدی رقم درج کریں۔',e_gold:'سونے کی درست قیمت اور وزن درج کریں۔',e_silver:'چاندی کی درست قیمت اور وزن درج کریں۔',e_nisab_gold:'سونے کے نصاب کے لیے سونا منتخب کریں اور درست قیمت درج کریں۔',e_nisab_silver:'چاندی کے نصاب کے لیے چاندی منتخب کریں اور درست قیمت درج کریں۔',
                        e_fp:'زکوٰۃ الفطر کے لیے افراد کی درست تعداد درج کریں۔',e_fa:'فی فرد زکوٰۃ کی درست اوسط رقم درج کریں۔',e_fkm:'فدیہ یا کفارہ منتخب کریں۔',e_fd:'چھوٹے ہوئے دن درست درج کریں۔',e_fda:'فی دن درست تخمینی رقم درج کریں۔',e_fkp:'افراد کی درست تعداد درج کریں۔',
                        h_fidya_t:'فدیہ (بیمار/معمر)',h_fidya_b:'فدیہ: ایسے معمر، دائمی مریض، یا حاملہ/دودھ پلانے والی خواتین پر ہوتا ہے جو بعد میں روزہ نہیں رکھ سکتیں؛ ہر چھوٹے ہوئے دن کے بدلے ایک مسکین کا کھانا۔',
                        h_kaff_t:'کفارہ (جان بوجھ کر روزہ توڑنا)',h_kaff_b:'کفارہ: بغیر شرعی عذر (مثلاً سفر یا بیماری) جان بوجھ کر روزہ توڑنے پر لازم ہوتا ہے؛ اور ترتیب سے قضا سے پہلے ادا کیا جاتا ہے۔',
                        cta_maal:'اپنی زکوٰۃ سے ضرورت مند مہاجر بچوں کو کھانا فراہم کریں',cta_fitr:'اپنی زکوٰۃ الفطر سے ضرورت مند مہاجر بچوں کو کھانا فراہم کریں',cta_fidya:'اپنے فدیے سے ضرورت مند مہاجر بچوں کو کھانا فراہم کریں',cta_kaff:'اپنے کفارے سے ضرورت مند مہاجر بچوں کو کھانا فراہم کریں'
                    },
                    tr:{
                        app_title:'Zekat Hesaplayici',app_sub:'',please_select:'lutfen secin:',
                        mode_maal:'Zakat al-Maal',mode_fitr:'Zakat al-Fitr',mode_fk:'Fidya / Kaffarah',
                        maal_title:'Zakat al-Maal',fitr_title:'Zakat al-Fitr',fk_title:'Fidya / Kaffarah',
                        maal_intro:"Zakat al-Maal, nisaba ulasmasi halinde bir kameri yil (hawl) sonunda toplam zekata tabi malin %2.5 (0.025) oraninda verilir. Su iki nisabtan biri esas alinir:\nAltin nisabi = 85 gram altin\nGumus nisabi = 595 gram gumus\nModern hesaplayicilarda bazi alimler gumus nisabini (fakirlere daha faydali) tercih eder, digerleri altin nisabini tercih eder.",
                        asset_cash:'Nakit',asset_gold:'Altin',asset_silver:'Gumus',cash_amount:'Nakit miktari (para)',gold_price:'Gram altin fiyati (para)',gold_weight:'Altin agirligi (gram)',silver_price:'Gram gumus fiyati (para)',silver_weight:'Gumus agirligi (gram)',
                        nisab_label:'Nisab standardi',nisab_gold:'Altin (85g)',nisab_silver:'Gumus (595g)',
                        fitr_intro:"Zakat al-Fitr kisi basi bir sa (yaklasik 2.5-3 kg temel gida veya nakit karsiligi) olarak verilir. ABD 2026 tahminine gore kisi basi $10-$15 civarindadir. Ev reisi, cocuklar dahil 5 kisilik hane icin bayram namazindan once odediginde toplam beklenen tutar $50-$75 olur.",
                        fitr_persons:'Kisi sayisi',fitr_amount:'Kisi basi ortalama zekat (para)',
                        fidya_label:'Fidya (hasta/yasli)',kaffarah_label:'Kaffarah (Kasti oruc bozma)',
                        fidya_intro:"Fidya, her kacirilan gun icin bir yoksulun yemegi olarak verilir. ABD 2026 tahmini gunluk yaklasik $12-$15 tir. Ramazanin tamami kacirilirsa (30 gun) beklenen fidya toplamı $360-$450 olur.",
                        kaffarah_intro:"Kaffarah, 60 yoksulu doyurmaktir. ABD 2026 tahminlerinde gunluk yaklasik $300-$600 araligindadir ve kaza oruclarindan once yerine getirilir.",
                        missed_days:'Kacirilan gun',amount_day:'Gunluk tahmini tutar (para)',persons:'Kisi sayisi',
                        btn_calc:'Hesapla',btn_reset:'Sifirla',btn_close:'Kapat',result_title:'Sonuc',
                        status_ok:'Uygun',status_bad:'Nisabin altinda',status_info:'Hesaplandi',
                        due_zakat:'Odenecek zekat',due_fitr:'Zakat al-Fitr toplami',due_fidya:'Fidya toplami',due_kaffarah:'Kaffarah toplami',
                        row_total:'Toplam mal',row_nisab:'Nisab degeri',row_nisab_std:'Nisab standardi',row_cash:'Nakit degeri',row_gold:'Altin degeri',row_silver:'Gumus degeri',row_mode:'Tur',row_amount_person:'Kisi basi tutar',row_amount_day:'Gunluk tutar',
                        summary:'Formul',std_gold:'Altin (85g)',std_silver:'Gumus (595g)',nisab_na:'Kontrol edilmedi',
                        e_asset:'Zakat al-Maal icin en az bir varlik secin.',e_cash:'Gecerli bir nakit miktari girin.',e_gold:'Gecerli altin fiyati ve agirligi girin.',e_silver:'Gecerli gumus fiyati ve agirligi girin.',e_nisab_gold:'Altin nisabi icin Altin secin ve gecerli altin fiyati girin.',e_nisab_silver:'Gumus nisabi icin Gumus secin ve gecerli gumus fiyati girin.',
                        e_fp:'Zakat al-Fitr icin gecerli kisi sayisi girin.',e_fa:'Kisi basi gecerli ortalama zekat girin.',e_fkm:'Fidya veya Kaffarah secin.',e_fd:'Gecerli kacirilan gun sayisi girin.',e_fda:'Gecerli gunluk tahmini tutar girin.',e_fkp:'Gecerli kisi sayisi girin.',
                        h_fidya_t:'Fidya (hasta/yasli)',h_fidya_b:'Fidya: Yasli, kronik hasta, hamile veya emziren ve sonradan orucu telafi edemeyen kisiler icin her kacirilan gun basina bir ogun.',
                        h_kaff_t:'Kaffarah (Kasti oruc bozma)',h_kaff_b:'Kaffarah: Gecerli mazeret olmadan (or. yolculuk/hastalik yokken) bilerek orucu bozma durumunda gerekir; siralamada kaza once degil, kaffarah once yerine getirilir.',
                        cta_maal:'Zekatiniz ile Ihtiyac Sahibi Multeci Cocuklara Yemek Ulastirin',cta_fitr:'Zakat al-Fitr ile Ihtiyac Sahibi Multeci Cocuklara Yemek Ulastirin',cta_fidya:'Fidyaniz ile Ihtiyac Sahibi Multeci Cocuklara Yemek Ulastirin',cta_kaff:'Kaffarah ile Ihtiyac Sahibi Multeci Cocuklara Yemek Ulastirin'
                    },
                    id:{
                        app_title:'Kalkulator Zakat',app_sub:'',please_select:'silakan pilih:',
                        mode_maal:'Zakat al-Maal',mode_fitr:'Zakat al-Fitr',mode_fk:'Fidya / Kaffarah',
                        maal_title:'Zakat al-Maal',fitr_title:'Zakat al-Fitr',fk_title:'Fidya / Kaffarah',
                        maal_intro:"Zakat al-Maal adalah 2,5% (0,025) dari total harta wajib zakat setelah satu tahun hijriah (hawl), jika mencapai nisab. Anda harus mencapai salah satu:\nNisab emas = 85 gram emas\nNisab perak = 595 gram perak\nPada kalkulator modern, sebagian ulama memakai nisab perak (lebih bermanfaat bagi fakir), sementara yang lain memakai nisab emas.",
                        asset_cash:'Tunai',asset_gold:'Emas',asset_silver:'Perak',cash_amount:'Jumlah tunai (uang)',gold_price:'Harga emas per gram (uang)',gold_weight:'Berat emas (gram)',silver_price:'Harga perak per gram (uang)',silver_weight:'Berat perak (gram)',
                        nisab_label:'Standar nisab',nisab_gold:'Emas (85g)',nisab_silver:'Perak (595g)',
                        fitr_intro:"Zakat al-Fitr adalah satu sha (sekitar 2,5-3 kg makanan pokok atau nilai tunai) per orang. Perkiraan di AS tahun 2026 sekitar $10-$15 per orang. Jika kepala keluarga membayar sebelum salat Id untuk 5 orang termasuk anak, total perkiraan kontribusi adalah $50-$75.",
                        fitr_persons:'Jumlah orang',fitr_amount:'Rata-rata zakat per orang (uang)',
                        fidya_label:'Fidya (sakit/lansia)',kaffarah_label:'Kaffarah (Sengaja membatalkan puasa)',
                        fidya_intro:"Fidya adalah memberi makan satu miskin per hari puasa yang terlewat. Perkiraan di AS tahun 2026 sekitar $12-$15 per hari. Jika 30 hari terlewat, total fidya yang diperkirakan adalah $360-$450.",
                        kaffarah_intro:"Kaffarah adalah memberi makan 60 orang miskin. Dalam estimasi AS tahun 2026, umumnya sekitar $300-$600 per hari dan harus ditunaikan sebelum qada puasa.",
                        missed_days:'Hari terlewat',amount_day:'Perkiraan jumlah per hari (uang)',persons:'Jumlah orang',
                        btn_calc:'Hitung',btn_reset:'Reset',btn_close:'Tutup',result_title:'Hasil Anda',
                        status_ok:'Memenuhi',status_bad:'Di bawah nisab',status_info:'Sudah dihitung',
                        due_zakat:'Zakat wajib',due_fitr:'Total Zakat al-Fitr',due_fidya:'Total Fidya',due_kaffarah:'Total Kaffarah',
                        row_total:'Total harta',row_nisab:'Nilai nisab',row_nisab_std:'Standar nisab',row_cash:'Nilai tunai',row_gold:'Nilai emas',row_silver:'Nilai perak',row_mode:'Mode',row_amount_person:'Jumlah per orang',row_amount_day:'Jumlah per hari',
                        summary:'Rumus',std_gold:'Emas (85g)',std_silver:'Perak (595g)',nisab_na:'Belum diperiksa',
                        e_asset:'Pilih minimal satu aset untuk Zakat al-Maal.',e_cash:'Masukkan jumlah tunai yang valid.',e_gold:'Masukkan harga dan berat emas yang valid.',e_silver:'Masukkan harga dan berat perak yang valid.',e_nisab_gold:'Untuk standar emas, pilih Emas dan isi harga emas yang valid.',e_nisab_silver:'Untuk standar perak, pilih Perak dan isi harga perak yang valid.',
                        e_fp:'Masukkan jumlah orang yang valid untuk Zakat al-Fitr.',e_fa:'Masukkan rata-rata zakat per orang yang valid.',e_fkm:'Pilih Fidya atau Kaffarah.',e_fd:'Masukkan jumlah hari terlewat yang valid.',e_fda:'Masukkan perkiraan jumlah per hari yang valid.',e_fkp:'Masukkan jumlah orang yang valid.',
                        h_fidya_t:'Fidya (sakit/lansia)',h_fidya_b:'Fidya: Dibayar oleh lansia, sakit kronis, ibu hamil/menyusui yang tidak mampu mengganti puasa; satu porsi makan untuk setiap hari terlewat.',
                        h_kaff_t:'Kaffarah (Sengaja membatalkan puasa)',h_kaff_b:'Kaffarah: Wajib bagi yang sengaja membatalkan puasa tanpa alasan syari (misalnya bukan safar/sakit); ditunaikan sebelum qada secara urut.',
                        cta_maal:'Salurkan Zakat Anda untuk Memberi Makan Anak Pengungsi yang Membutuhkan',cta_fitr:'Salurkan Zakat al-Fitr Anda untuk Memberi Makan Anak Pengungsi yang Membutuhkan',cta_fidya:'Salurkan Fidya Anda untuk Memberi Makan Anak Pengungsi yang Membutuhkan',cta_kaff:'Salurkan Kaffarah Anda untuk Memberi Makan Anak Pengungsi yang Membutuhkan'
                    },
                    de:{
                        app_title:'Zakat-Rechner',app_sub:'',please_select:'bitte auswaehlen:',
                        mode_maal:'Zakat al-Maal',mode_fitr:'Zakat al-Fitr',mode_fk:'Fidya / Kaffarah',
                        maal_title:'Zakat al-Maal',fitr_title:'Zakat al-Fitr',fk_title:'Fidya / Kaffarah',
                        maal_intro:"Zakat al-Maal betraegt 2,5% (0,025) des gesamten zakatpflichtigen Vermoegens nach einem Mondjahr (hawl), sofern der Nisab erreicht wird. Es gilt einer der folgenden Werte:\nGold-Nisab = 85 Gramm Gold\nSilber-Nisab = 595 Gramm Silber\nIn modernen Rechnern verwenden einige Gelehrte den Silber-Nisab (guenstiger fuer Arme), andere den Gold-Nisab.",
                        asset_cash:'Bargeld',asset_gold:'Gold',asset_silver:'Silber',cash_amount:'Bargeldbetrag (Geld)',gold_price:'Goldpreis pro Gramm (Geld)',gold_weight:'Goldgewicht (Gramm)',silver_price:'Silberpreis pro Gramm (Geld)',silver_weight:'Silbergewicht (Gramm)',
                        nisab_label:'Nisab-Standard',nisab_gold:'Gold (85g)',nisab_silver:'Silber (595g)',
                        fitr_intro:"Zakat al-Fitr entspricht einer Sa (ca. 2,5-3 kg Grundnahrungsmittel oder Geldwert) pro Person. In den USA wird sie 2026 auf etwa $10-$15 pro Person geschaetzt. Zahlt ein Familienoberhaupt vor dem Eid-Gebet fuer 5 Personen inklusive Kinder, liegt der erwartete Gesamtbetrag bei $50-$75.",
                        fitr_persons:'Anzahl Personen',fitr_amount:'Durchschnitt pro Person (Geld)',
                        fidya_label:'Fidya (krank/aelter)',kaffarah_label:'Kaffarah (Absichtliches Fastenbrechen)',
                        fidya_intro:"Fidya bedeutet eine Mahlzeit fuer eine beduerftige Person pro versaeumtem Fastentag. US-Schaetzung fuer 2026: etwa $12-$15 pro Tag. Bei 30 versaeumten Tagen liegt der erwartete Gesamtbetrag bei $360-$450.",
                        kaffarah_intro:"Kaffarah bedeutet, 60 beduerftige Personen zu speisen. In den US-Schaetzungen fuer 2026 liegt es haeufig bei etwa $300-$600 pro Tag und wird vor den Nachholfasten (qada) erfuellt.",
                        missed_days:'Versaeumte Tage',amount_day:'Geschaetzter Betrag pro Tag (Geld)',persons:'Anzahl Personen',
                        btn_calc:'Berechnen',btn_reset:'Zuruecksetzen',btn_close:'Schliessen',result_title:'Ihr Ergebnis',
                        status_ok:'Pflichtig',status_bad:'Unter Nisab',status_info:'Berechnet',
                        due_zakat:'Faellige Zakat',due_fitr:'Zakat al-Fitr gesamt',due_fidya:'Fidya gesamt',due_kaffarah:'Kaffarah gesamt',
                        row_total:'Gesamtvermoegen',row_nisab:'Nisab-Wert',row_nisab_std:'Nisab-Standard',row_cash:'Bargeldwert',row_gold:'Goldwert',row_silver:'Silberwert',row_mode:'Modus',row_amount_person:'Betrag pro Person',row_amount_day:'Betrag pro Tag',
                        summary:'Formel',std_gold:'Gold (85g)',std_silver:'Silber (595g)',nisab_na:'Nicht geprueft',
                        e_asset:'Waehlen Sie mindestens eine Vermoegensart fuer Zakat al-Maal.',e_cash:'Geben Sie einen gueltigen Bargeldbetrag ein.',e_gold:'Geben Sie gueltigen Goldpreis und gueltiges Goldgewicht ein.',e_silver:'Geben Sie gueltigen Silberpreis und gueltiges Silbergewicht ein.',e_nisab_gold:'Fuer Gold-Nisab bitte Gold waehlen und gueltigen Goldpreis eingeben.',e_nisab_silver:'Fuer Silber-Nisab bitte Silber waehlen und gueltigen Silberpreis eingeben.',
                        e_fp:'Geben Sie eine gueltige Personenzahl fuer Zakat al-Fitr ein.',e_fa:'Geben Sie einen gueltigen Durchschnittsbetrag pro Person ein.',e_fkm:'Waehlen Sie Fidya oder Kaffarah.',e_fd:'Geben Sie gueltige versaeumte Tage ein.',e_fda:'Geben Sie einen gueltigen Tagesbetrag ein.',e_fkp:'Geben Sie eine gueltige Personenzahl ein.',
                        h_fidya_t:'Fidya (krank/aelter)',h_fidya_b:'Fidya: Fuer aeltere Menschen, chronisch Kranke sowie schwangere/stillende Frauen, die spaeter nicht nachfasten koennen; eine Mahlzeit pro versaeumtem Tag.',
                        h_kaff_t:'Kaffarah (Absichtliches Fastenbrechen)',h_kaff_b:'Kaffarah: Faellig bei bewusstem Fastenbrechen ohne gueltigen Grund (z. B. keine Reise/Krankheit); in der Reihenfolge vor qada zu leisten.',
                        cta_maal:'Geben Sie Ihre Zakat, um beduerftige Flüchtlingskinder zu ernaehren',cta_fitr:'Geben Sie Ihre Zakat al-Fitr, um beduerftige Flüchtlingskinder zu ernaehren',cta_fidya:'Geben Sie Ihre Fidya, um beduerftige Flüchtlingskinder zu ernaehren',cta_kaff:'Geben Sie Ihre Kaffarah, um beduerftige Flüchtlingskinder zu ernaehren'
                    },
                    es:{
                        app_title:'Calculadora de Zakat',app_sub:'',please_select:'por favor seleccione:',
                        mode_maal:'Zakat al-Maal',mode_fitr:'Zakat al-Fitr',mode_fk:'Fidya / Kaffarah',
                        maal_title:'Zakat al-Maal',fitr_title:'Zakat al-Fitr',fk_title:'Fidya / Kaffarah',
                        maal_intro:"Zakat al-Maal es el 2,5% (0,025) de la riqueza total sujeta a zakat tras un ano lunar (hawl), si alcanza el nisab. Debe alcanzar uno de estos:\nNisab de oro = 85 gramos de oro\nNisab de plata = 595 gramos de plata\nEn calculadoras modernas, algunos eruditos usan nisab de plata (beneficia mas a los pobres) y otros usan nisab de oro.",
                        asset_cash:'Efectivo',asset_gold:'Oro',asset_silver:'Plata',cash_amount:'Cantidad en efectivo (dinero)',gold_price:'Precio del oro por gramo (dinero)',gold_weight:'Peso del oro (gramos)',silver_price:'Precio de la plata por gramo (dinero)',silver_weight:'Peso de la plata (gramos)',
                        nisab_label:'Estandar de nisab',nisab_gold:'Oro (85g)',nisab_silver:'Plata (595g)',
                        fitr_intro:"Zakat al-Fitr es un sa (aprox. 2,5-3 kg de alimento basico o equivalente en efectivo) por persona. En EE. UU. para 2026 se estima en $10-$15 por persona. Si el jefe de familia paga antes de la oracion del Eid por un hogar de 5 personas incluyendo ninos, el total estimado es $50-$75.",
                        fitr_persons:'Numero de personas',fitr_amount:'Promedio por persona (dinero)',
                        fidya_label:'Fidya (enfermo/anciano)',kaffarah_label:'Kaffarah (Romper el ayuno intencionalmente)',
                        fidya_intro:"Fidya es alimentar a una persona necesitada por cada dia de ayuno perdido. En EE. UU. para 2026 se estima en $12-$15 por dia. Si se pierden 30 dias, el total esperado es $360-$450.",
                        kaffarah_intro:"Kaffarah es alimentar a 60 personas pobres. En estimaciones de EE. UU. para 2026 suele estar entre $300-$600 por dia y debe cumplirse antes del qada (ayunos de reposicion).",
                        missed_days:'Dias perdidos',amount_day:'Monto estimado por dia (dinero)',persons:'Numero de personas',
                        btn_calc:'Calcular',btn_reset:'Restablecer',btn_close:'Cerrar',result_title:'Su resultado',
                        status_ok:'Corresponde',status_bad:'Por debajo del nisab',status_info:'Calculado',
                        due_zakat:'Zakat debida',due_fitr:'Total Zakat al-Fitr',due_fidya:'Total Fidya',due_kaffarah:'Total Kaffarah',
                        row_total:'Riqueza total',row_nisab:'Valor del nisab',row_nisab_std:'Estandar de nisab',row_cash:'Valor en efectivo',row_gold:'Valor del oro',row_silver:'Valor de la plata',row_mode:'Modo',row_amount_person:'Monto por persona',row_amount_day:'Monto por dia',
                        summary:'Formula',std_gold:'Oro (85g)',std_silver:'Plata (595g)',nisab_na:'No verificado',
                        e_asset:'Seleccione al menos un activo para Zakat al-Maal.',e_cash:'Ingrese una cantidad de efectivo valida.',e_gold:'Ingrese precio y peso de oro validos.',e_silver:'Ingrese precio y peso de plata validos.',e_nisab_gold:'Para usar nisab de oro, seleccione Oro e ingrese un precio valido.',e_nisab_silver:'Para usar nisab de plata, seleccione Plata e ingrese un precio valido.',
                        e_fp:'Ingrese un numero valido de personas para Zakat al-Fitr.',e_fa:'Ingrese un promedio valido por persona.',e_fkm:'Seleccione Fidya o Kaffarah.',e_fd:'Ingrese dias perdidos validos.',e_fda:'Ingrese un monto diario estimado valido.',e_fkp:'Ingrese un numero valido de personas.',
                        h_fidya_t:'Fidya (enfermo/anciano)',h_fidya_b:'Fidya: La pagan ancianos, enfermos cronicos o mujeres embarazadas/lactantes que no pueden recuperar el ayuno despues; una comida por cada dia perdido.',
                        h_kaff_t:'Kaffarah (Romper el ayuno intencionalmente)',h_kaff_b:'Kaffarah: Corresponde por romper el ayuno conscientemente sin motivo valido (por ejemplo, sin viaje/enfermedad); debe realizarse antes del qada en secuencia.',
                        cta_maal:'Entregue su Zakat para alimentar a ninos refugiados necesitados',cta_fitr:'Entregue su Zakat al-Fitr para alimentar a ninos refugiados necesitados',cta_fidya:'Entregue su Fidya para alimentar a ninos refugiados necesitados',cta_kaff:'Entregue su Kaffarah para alimentar a ninos refugiados necesitados'
                    },
                    fr:{
                        app_title:'Calculateur de Zakat',app_sub:'',please_select:'veuillez selectionner :',
                        mode_maal:'Zakat al-Maal',mode_fitr:'Zakat al-Fitr',mode_fk:'Fidya / Kaffarah',
                        maal_title:'Zakat al-Maal',fitr_title:'Zakat al-Fitr',fk_title:'Fidya / Kaffarah',
                        maal_intro:"La Zakat al-Maal est de 2,5 % (0,025) du patrimoine total soumis a la zakat apres une annee lunaire (hawl), si le nisab est atteint. Vous devez atteindre au moins un des deux seuils :\nNisab or = 85 grammes d or\nNisab argent = 595 grammes d argent\nDans les calculateurs modernes, certains savants utilisent le nisab argent (plus favorable aux pauvres), d autres utilisent le nisab or.",
                        asset_cash:'Especes',asset_gold:'Or',asset_silver:'Argent',cash_amount:'Montant en especes (argent)',gold_price:'Prix de l or par gramme (argent)',gold_weight:'Poids de l or (grammes)',silver_price:'Prix de l argent par gramme (argent)',silver_weight:'Poids de l argent (grammes)',
                        nisab_label:'Standard du nisab',nisab_gold:'Or (85g)',nisab_silver:'Argent (595g)',
                        fitr_intro:"La Zakat al-Fitr correspond a un sa (environ 2,5-3 kg de nourriture de base ou son equivalent en argent) par personne. Aux Etats-Unis en 2026, elle est estimee a $10-$15 par personne. Si le chef de famille paie avant la priere de l Eid pour un foyer de 5 personnes incluant les enfants, le total estime est de $50-$75.",
                        fitr_persons:'Nombre de personnes',fitr_amount:'Montant moyen par personne (argent)',
                        fidya_label:'Fidya (malade/age)',kaffarah_label:'Kaffarah (Rupture intentionnelle du jeune)',
                        fidya_intro:"La Fidya consiste a nourrir une personne pauvre pour chaque jour manque. Aux Etats-Unis en 2026, elle est estimee a $12-$15 par jour. Si 30 jours sont manques, le total attendu est de $360-$450.",
                        kaffarah_intro:"La Kaffarah consiste a nourrir 60 personnes pauvres. Dans les estimations americaines de 2026, elle est souvent autour de $300-$600 par jour et doit etre accomplie avant les jours de rattrapage (qada).",
                        missed_days:'Jours manques',amount_day:'Montant estime par jour (argent)',persons:'Nombre de personnes',
                        btn_calc:'Calculer',btn_reset:'Reinitialiser',btn_close:'Fermer',result_title:'Votre resultat',
                        status_ok:'Eligible',status_bad:'Sous le nisab',status_info:'Calcule',
                        due_zakat:'Zakat due',due_fitr:'Total Zakat al-Fitr',due_fidya:'Total Fidya',due_kaffarah:'Total Kaffarah',
                        row_total:'Patrimoine total',row_nisab:'Valeur du nisab',row_nisab_std:'Standard du nisab',row_cash:'Valeur en especes',row_gold:'Valeur de l or',row_silver:'Valeur de l argent',row_mode:'Mode',row_amount_person:'Montant par personne',row_amount_day:'Montant par jour',
                        summary:'Formule',std_gold:'Or (85g)',std_silver:'Argent (595g)',nisab_na:'Non verifie',
                        e_asset:'Selectionnez au moins un actif pour la Zakat al-Maal.',e_cash:'Entrez un montant en especes valide.',e_gold:'Entrez un prix et un poids de l or valides.',e_silver:'Entrez un prix et un poids de l argent valides.',e_nisab_gold:'Pour utiliser le nisab or, selectionnez Or et entrez un prix valide.',e_nisab_silver:'Pour utiliser le nisab argent, selectionnez Argent et entrez un prix valide.',
                        e_fp:'Entrez un nombre de personnes valide pour la Zakat al-Fitr.',e_fa:'Entrez un montant moyen valide par personne.',e_fkm:'Selectionnez Fidya ou Kaffarah.',e_fd:'Entrez un nombre de jours manques valide.',e_fda:'Entrez un montant journalier estime valide.',e_fkp:'Entrez un nombre de personnes valide.',
                        h_fidya_t:'Fidya (malade/age)',h_fidya_b:'Fidya : due par les personnes agees, malades chroniques ou femmes enceintes/allaitantes ne pouvant pas rattraper le jeune; un repas par jour manque.',
                        h_kaff_t:'Kaffarah (Rupture intentionnelle du jeune)',h_kaff_b:'Kaffarah : due en cas de rupture volontaire du jeune sans raison valable (par ex. pas de voyage/maladie); doit preceder le qada dans l ordre.',
                        cta_maal:'Donnez votre Zakat pour nourrir des enfants refugies dans le besoin',cta_fitr:'Donnez votre Zakat al-Fitr pour nourrir des enfants refugies dans le besoin',cta_fidya:'Donnez votre Fidya pour nourrir des enfants refugies dans le besoin',cta_kaff:'Donnez votre Kaffarah pour nourrir des enfants refugies dans le besoin'
                    }
                };

                function tr(k){var p=t[state.lang]||{};return Object.prototype.hasOwnProperty.call(p,k)?p[k]:(t.en[k]||k);}    
                function qs(s){return root.querySelector(s);} function qsa(s){return Array.prototype.slice.call(root.querySelectorAll(s));}
                function f(n){return qs('[data-f="'+n+'"]');}
                function n(nm){var e=f(nm);if(!e){return 0;}var v=parseFloat(e.value);return !isFinite(v)||v<0?0:v;}
                function i(nm){var v=Math.floor(n(nm));return v<0?0:v;}
                function money(v){return Number(v).toFixed(2);}    
                function noCurrency(v){return String(v).replace(/\$/g,'').trim();}
                function txt(){qsa('[data-t]').forEach(function(el){var k=el.getAttribute('data-t');if(el.tagName==='OPTION'){el.textContent=tr(k);}else{el.textContent=tr(k);}});}    
                function setLang(code,flag,shortCode,isRtl){state.lang=code;qs('[data-r="lang-flag"]').textContent=flag;qs('[data-r="lang-code"]').textContent=shortCode;root.classList.toggle('zscu-rtl',isRtl);txt();toggleFkInfo();if(state.last){render(state.last);}}
                function softScroll(el){
                    if(!el){return;}
                    var box=qs('.zscu-content');
                    if(!box){return;}
                    var target=Math.max(0,el.offsetTop-8);
                    var start=box.scrollTop;
                    var delta=target-start;
                    if(Math.abs(delta)<2){return;}
                    var duration=1600;
                    var t0=null;
                    function ease(t){return t<0.5?2*t*t:1-Math.pow(-2*t+2,2)/2;}
                    function step(ts){
                        if(t0===null){t0=ts;}
                        var p=Math.min((ts-t0)/duration,1);
                        box.scrollTop=start+(delta*ease(p));
                        if(p<1){requestAnimationFrame(step);}
                    }
                    requestAnimationFrame(step);
                }
                function setMode(m,skipScroll){state.mode=m;qsa('.zscu-mode').forEach(function(b){b.classList.toggle('active',b.getAttribute('data-mode')===m);});qsa('.zscu-panel').forEach(function(p){p.classList.toggle('active',p.getAttribute('data-panel')===m);});clearErr();updateSubmit();if(!skipScroll){softScroll(qs('[data-panel=\"'+m+'\"]'));}}
                function err(msg){var e=qs('[data-r="err"]');e.hidden=false;e.textContent=msg;}
                function clearErr(){var e=qs('[data-r="err"]');e.hidden=true;e.textContent='';}
                function clearFieldErrors(){qsa('.zscu-input-error').forEach(function(el){el.classList.remove('zscu-input-error');});qsa('.zscu-select-error').forEach(function(el){el.classList.remove('zscu-select-error');});qsa('.zscu-check-error').forEach(function(el){el.classList.remove('zscu-check-error');});qsa('.zscu-choice-error').forEach(function(el){el.classList.remove('zscu-choice-error');});}
                function fkMode(){var m=qs('input[data-f="fkm"]:checked');return m?m.value:'';}
                function toggleAssets(){var cash=qs('[data-sec=\"cash\"]'),gold=qs('[data-sec=\"gold\"]'),silver=qs('[data-sec=\"silver\"]');var showCash=f('asset_cash').checked,showGold=f('asset_gold').checked,showSilver=f('asset_silver').checked;var reveal=null;if(showCash&&cash.style.display==='none'){reveal=cash;}if(showGold&&gold.style.display==='none'){reveal=gold;}if(showSilver&&silver.style.display==='none'){reveal=silver;}cash.style.display=showCash?'block':'none';gold.style.display=showGold?'grid':'none';silver.style.display=showSilver?'grid':'none';if(reveal){softScroll(reveal);}}
                function toggleFkInfo(){var m=fkMode();var fid=qs('[data-info=\"fidya\"]'),kaf=qs('[data-info=\"kaffarah\"]');fid.style.display=m==='fidya'?'block':'none';kaf.style.display=m==='kaffarah'?'block':'none';if(m==='fidya'){softScroll(fid);}if(m==='kaffarah'){softScroll(kaf);}}
                function valid(silent){
                    var e='';
                    var firstBad=null;
                    function mark(el, cls){if(silent||!el){return;}el.classList.add(cls);if(!firstBad){firstBad=el;}}
                    if(!silent){clearFieldErrors();}
                    if(state.mode==='maal'){
                        var hasC=f('asset_cash').checked,hasG=f('asset_gold').checked,hasS=f('asset_silver').checked;
                        if(!hasC&&!hasG&&!hasS){e='e_asset';qsa('.zscu-check').forEach(function(c){mark(c,'zscu-check-error');});}
                        else if(hasC&&n('cash')<=0){e='e_cash';mark(f('cash'),'zscu-input-error');}
                        else if(hasG&&(n('gp')<=0||n('gw')<=0)){e='e_gold';if(n('gp')<=0){mark(f('gp'),'zscu-input-error');}if(n('gw')<=0){mark(f('gw'),'zscu-input-error');}}
                        else if(hasS&&(n('sp')<=0||n('sw')<=0)){e='e_silver';if(n('sp')<=0){mark(f('sp'),'zscu-input-error');}if(n('sw')<=0){mark(f('sw'),'zscu-input-error');}}
                        else if(f('nisab').value==='gold'&&hasG&&n('gp')<=0){e='e_nisab_gold';mark(f('gp'),'zscu-input-error');}
                        else if(f('nisab').value==='silver'&&hasS&&n('sp')<=0){e='e_nisab_silver';mark(f('sp'),'zscu-input-error');}
                    }
                    if(state.mode==='fitr'){
                        if(i('fp')<=0){e='e_fp';mark(f('fp'),'zscu-input-error');} else if(n('fa')<=0){e='e_fa';mark(f('fa'),'zscu-input-error');}
                    }
                    if(state.mode==='fk'){
                        if(!fkMode()){e='e_fkm';qsa('.zscu-choice').forEach(function(c){mark(c,'zscu-choice-error');});}
                        else if(i('fd')<=0){e='e_fd';mark(f('fd'),'zscu-input-error');}
                        else if(n('fda')<=0){e='e_fda';mark(f('fda'),'zscu-input-error');}
                        else if(i('fkp')<=0){e='e_fkp';mark(f('fkp'),'zscu-input-error');}
                    }
                    if(!silent){if(e){err(tr(e));if(firstBad){softScroll(firstBad);}}else{clearErr();}}
                    return !e;
                }
                function updateSubmit(){var btn=qs('[data-r="submit"]');var ok=valid(true);btn.classList.toggle('is-disabled',!ok);btn.setAttribute('aria-disabled',ok?'false':'true');if(ok){clearFieldErrors();clearErr();}}    
                function row(label,val){var r=document.createElement('div');r.className='zscu-row';var s1=document.createElement('span');s1.textContent=label;var s2=document.createElement('strong');s2.textContent=noCurrency(val);r.appendChild(s1);r.appendChild(s2);return r;}
                function render(res){var wrap=qs('[data-r="result-wrap"]');var m=qs('[data-r="result"]');m.innerHTML='';var card=document.createElement('div');card.className='zscu-result';var hd=document.createElement('div');hd.className='zscu-result-head';var st=document.createElement('div');st.className='zscu-status '+res.sc;st.textContent=res.st;hd.appendChild(st);var a=document.createElement('span');a.className='zscu-amount-inline';a.textContent=noCurrency(money(res.amount));hd.appendChild(a);var al=document.createElement('span');al.className='zscu-amount-inline-label';al.textContent=res.al;hd.appendChild(al);card.appendChild(hd);var rows=document.createElement('div');rows.className='zscu-rows';res.rows.forEach(function(it){rows.appendChild(row(it.l,it.v));});card.appendChild(rows);var sum=document.createElement('div');sum.className='zscu-summary';sum.textContent=tr('summary')+': '+noCurrency(res.sum);card.appendChild(sum);if(showCta&&res.cta){var c=document.createElement('div');c.className='zscu-cta';c.textContent=res.cta;card.appendChild(c);}m.appendChild(card);wrap.hidden=false;softScroll(wrap);}
                function calc(){if(!valid(false)){return;}var res;
                    if(state.mode==='maal'){
                        var c=f('asset_cash').checked?n('cash'):0,gp=f('asset_gold').checked?n('gp'):0,gw=f('asset_gold').checked?n('gw'):0,sp=f('asset_silver').checked?n('sp'):0,sw=f('asset_silver').checked?n('sw'):0;
                        var gv=gp*gw,sv=sp*sw,total=c+gv+sv,nisType=f('nisab').value,canCheck=(nisType==='gold'?gp>0:sp>0),nis=canCheck?(nisType==='gold'?85*gp:595*sp):0,ok=canCheck?(total>=nis&&nis>0):true,due=canCheck?(ok?total*0.025:0):total*0.025;
                        var rows=[];if(f('asset_cash').checked){rows.push({l:tr('row_cash'),v:money(c)});}if(f('asset_gold').checked){rows.push({l:tr('row_gold'),v:money(gv)});}if(f('asset_silver').checked){rows.push({l:tr('row_silver'),v:money(sv)});}rows.push({l:tr('row_total'),v:money(total)});rows.push({l:tr('row_nisab_std'),v:nisType==='gold'?tr('std_gold'):tr('std_silver')});rows.push({l:tr('row_nisab'),v:canCheck?money(nis):tr('nisab_na')});
                        res={sc:canCheck?(ok?'ok':'bad'):'info',st:canCheck?(ok?tr('status_ok'):tr('status_bad')):tr('status_info'),amount:due,al:tr('due_zakat'),rows:rows,sum:money(total)+' x 2.5% = '+money(due),cta:tr('cta_maal')};
                    }
                    if(state.mode==='fitr'){
                        var p=i('fp'),a=n('fa'),tot=p*a;
                        res={sc:'info',st:tr('status_info'),amount:tot,al:tr('due_fitr'),rows:[{l:tr('persons'),v:String(p)},{l:tr('row_amount_person'),v:money(a)}],sum:p+' '+tr('persons').toLowerCase()+' x '+money(a)+' = '+money(tot),cta:tr('cta_fitr')};
                    }
                    if(state.mode==='fk'){
                        var mode=fkMode(),d=i('fd'),da=n('fda'),p2=i('fkp'),tot2=d*da*p2,isF=mode==='fidya';
                        res={sc:'info',st:tr('status_info'),amount:tot2,al:isF?tr('due_fidya'):tr('due_kaffarah'),rows:[{l:tr('row_mode'),v:isF?tr('fidya_label'):tr('kaffarah_label')},{l:tr('missed_days'),v:String(d)},{l:tr('row_amount_day'),v:money(da)},{l:tr('persons'),v:String(p2)}],sum:d+' x '+money(da)+' x '+p2+' = '+money(tot2),cta:isF?tr('cta_fidya'):tr('cta_kaff')};
                    }
                    state.last=res;render(res);
                }
                function reset(){clearErr();state.last=null;qs('[data-r="result-wrap"]').hidden=true;qs('[data-r="result"]').innerHTML='';if(state.mode==='maal'){['cash','gp','gw','sp','sw'].forEach(function(k){f(k).value='';});f('asset_cash').checked=true;f('asset_gold').checked=false;f('asset_silver').checked=false;f('nisab').value='gold';toggleAssets();}if(state.mode==='fitr'){f('fp').value='';f('fa').value='';}if(state.mode==='fk'){qsa('input[data-f="fkm"]').forEach(function(r){r.checked=false;});f('fd').value='';f('fda').value='';f('fkp').value='';toggleFkInfo();}updateSubmit();}
                function openModal(tt,bb){qs('[data-r="modal-title"]').textContent=tt;qs('[data-r="modal-body"]').textContent=bb;var bg=qs('[data-r="modal-bg"]');bg.classList.add('open');bg.setAttribute('aria-hidden','false');}
                function closeModal(){var bg=qs('[data-r="modal-bg"]');bg.classList.remove('open');bg.setAttribute('aria-hidden','true');}

                qs('[data-r="lang-toggle"]').addEventListener('click',function(){var m=qs('[data-r="lang-menu"]');var open=m.classList.toggle('open');qs('[data-r="lang-toggle"]').setAttribute('aria-expanded',open?'true':'false');});
                qsa('.zscu-lang-item').forEach(function(b){b.addEventListener('click',function(){setLang(b.getAttribute('data-lang'),b.getAttribute('data-flag'),b.getAttribute('data-code'),b.getAttribute('data-rtl')==='1');qs('[data-r="lang-menu"]').classList.remove('open');qs('[data-r="lang-toggle"]').setAttribute('aria-expanded','false');});});
                root.addEventListener('click',function(e){var m=qs('[data-r="lang-menu"]'),tgg=qs('[data-r="lang-toggle"]');if(m.classList.contains('open')&&!m.contains(e.target)&&!tgg.contains(e.target)){m.classList.remove('open');tgg.setAttribute('aria-expanded','false');}});
                document.addEventListener('click',function(e){if(!root.contains(e.target)){qs('[data-r="lang-menu"]').classList.remove('open');qs('[data-r="lang-toggle"]').setAttribute('aria-expanded','false');}});
                qsa('.zscu-mode').forEach(function(b){b.addEventListener('click',function(){setMode(b.getAttribute('data-mode'));});});
                ['asset_cash','asset_gold','asset_silver','cash','gp','gw','sp','sw','nisab','fp','fa','fd','fda','fkp'].forEach(function(k){var el=f(k);if(el){el.addEventListener('input',updateSubmit);el.addEventListener('change',function(){if(k.indexOf('asset_')===0){toggleAssets();}updateSubmit();});}});
                qsa('input[data-f="fkm"]').forEach(function(r){r.addEventListener('change',function(){toggleFkInfo();updateSubmit();});});
                qsa('.zscu-help').forEach(function(b){b.addEventListener('click',function(){if(b.getAttribute('data-help')==='fidya'){openModal(tr('h_fidya_t'),tr('h_fidya_b'));}else{openModal(tr('h_kaff_t'),tr('h_kaff_b'));}});});
                qs('[data-r="modal-close"]').addEventListener('click',closeModal);
                qs('[data-r="modal-bg"]').addEventListener('click',function(e){if(e.target===qs('[data-r="modal-bg"]')){closeModal();}});
                root.addEventListener('keydown',function(e){if(e.key==='Escape'){closeModal();qs('[data-r="lang-menu"]').classList.remove('open');qs('[data-r="lang-toggle"]').setAttribute('aria-expanded','false');}});
                qs('[data-r="submit"]').addEventListener('click',calc);
                qs('[data-r="reset"]').addEventListener('click',reset);

                txt();toggleAssets();toggleFkInfo();setMode(initialMode,true);updateSubmit();
            })();
        </script>
    </div>
    <?php
    return ob_get_clean();
}

function zscu_normalize_default_mode($raw) {
    $v = strtolower(trim((string) $raw));

    if (in_array($v, array('1', 'fitr', 'zakat al fitr', 'zakat_al_fitr', 'zakat-al-fitr'), true)) {
        return 'fitr';
    }

    if (in_array($v, array('2', 'fk', 'fidya', 'kaffarah', 'fidya kaffarah', 'fidya_kaffarah', 'fidya-kaffarah', 'kaffarah fidya', 'kaffarah_fidya', 'kaffarah-fidya'), true)) {
        return 'fk';
    }

    if (in_array($v, array('3', 'maal', 'zakat', 'zakat al maal', 'zakat_al_maal', 'zakat-al-maal'), true)) {
        return 'maal';
    }

    return 'maal';
}

add_action('admin_menu', function () {
    add_options_page('Zakat Ultimate', 'Zakat Ultimate', 'manage_options', 'zakat-ultimate', 'zscu_admin');
});

function zscu_admin() {
    ?>
    <div class="wrap">
        <h1>Zakat Ultimate v3.2</h1>
        <div class="notice notice-info">
            <p><code>[zakat_ultimate]</code> - Standard Ramadan calculator</p>
            <p><code>[zakat_ultimate_pro]</code> - Same calculator + CTA headline block in results</p>
            <p><strong>Default mode attribute:</strong> <code>default=\"1\"</code> (Fitr), <code>default=\"2\"</code> (Fidya/Kaffarah), <code>default=\"3\"</code> (Maal)</p>
            <p>Also supported: <code>default=\"fitr\"</code>, <code>default=\"fk\"</code>, <code>default=\"maal\"</code></p>
        </div>
        <h3>Ramadan v1 Features</h3>
        <ul>
            <li>Compact fixed-size single-card UX</li>
            <li>Top-corner language switcher (default English)</li>
            <li>Zakat al-Maal with Cash/Gold/Silver multi-select and Nisab check</li>
            <li>Zakat al-Fitr quick calculator</li>
            <li>Fidya / Kaffarah single-choice mode with help popups</li>
            <li>Improved wording and calculation summary lines</li>
            <li>Single-file plugin architecture (lightweight)</li>
        </ul>
    </div>
    <?php
}
