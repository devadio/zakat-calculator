<?php
/**
 * Plugin Name: Zakat Ultimate
 * Description: Ultimate lightweight Zakat & Sadaqah calculator with correct Islamic calculations
 * Version: 3.1.0
 * Author: Custom Plugin
 * Text Domain: zakat-ultimate
 */

if (!defined('ABSPATH')) exit;

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
    $cta_url = 'https://lph.elvefa.org/';
    
    ob_start();
    ?>
    <div id="<?php echo esc_attr($id); ?>" class="zscu-wrap">
        <style>
            /* Base - Fixed container */
            .zscu-wrap {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                max-width: 600px;
                margin: 20px auto;
                background: #fff;
                border-radius: 16px;
                box-shadow: 0 4px 24px rgba(0,0,0,0.08);
                padding: 24px;
                min-height: 520px;
                max-height: 620px;
                display: flex;
                flex-direction: column;
                position: relative;
                overflow: hidden;
            }
            
            .zscu-content {
                flex: 1;
                overflow-y: auto;
                padding-right: 8px;
                margin-right: -8px;
            }
            
            .zscu-content::-webkit-scrollbar { width: 6px; }
            .zscu-content::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 3px; }
            .zscu-content::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }
            
            .zscu-step { display: none; animation: zscuFadeIn 0.3s ease; }
            .zscu-step.active { display: block; }
            @keyframes zscuFadeIn { from { opacity: 0; } to { opacity: 1; } }
            
            /* Progress */
            .zscu-progress {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 8px;
                margin-bottom: 20px;
                flex-shrink: 0;
            }
            .zscu-step-dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: #e0e0e0;
                transition: 0.3s;
            }
            .zscu-step-dot.active { background: #1a5f3c; transform: scale(1.3); }
            .zscu-step-dot.completed { background: #2e8b57; }
            .zscu-step-line {
                width: 20px;
                height: 2px;
                background: #e0e0e0;
            }
            .zscu-step-line.completed { background: #2e8b57; }
            
            /* Typography */
            .zscu-title {
                text-align: center;
                font-size: 20px;
                font-weight: 700;
                color: #1a5f3c;
                margin-bottom: 4px;
            }
            .zscu-subtitle {
                text-align: center;
                color: #666;
                font-size: 13px;
                margin-bottom: 16px;
            }
            
            /* Language Search */
            .zscu-search {
                width: 100%;
                padding: 12px 16px;
                border: 2px solid #e5e7eb;
                border-radius: 10px;
                font-size: 14px;
                margin-bottom: 12px;
                box-sizing: border-box;
            }
            .zscu-search:focus {
                outline: none;
                border-color: #1a5f3c;
            }
            
            /* Language List */
            .zscu-langs {
                max-height: 280px;
                overflow-y: auto;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
            }
            .zscu-lang {
                display: flex;
                align-items: center;
                padding: 10px 14px;
                cursor: pointer;
                border-bottom: 1px solid #f3f4f6;
                transition: 0.15s;
            }
            .zscu-lang:hover { background: #f9fafb; }
            .zscu-lang.selected { background: #f0fdf4; border-left: 3px solid #1a5f3c; }
            .zscu-lang .flag { font-size: 22px; margin-right: 12px; }
            .zscu-lang .name { font-weight: 600; color: #1f2937; font-size: 14px; }
            
            /* Cards Grid */
            .zscu-cards {
                display: grid;
                gap: 10px;
                margin-bottom: 16px;
            }
            
            /* MAIN TYPE Cards (Zakat/Sadaqah) - LARGE */
            .zscu-cards.main-types {
                grid-template-columns: repeat(2, 1fr);
            }
            .zscu-card-main {
                border: 2px solid #e5e7eb;
                border-radius: 12px;
                padding: 20px 16px;
                text-align: center;
                cursor: pointer;
                transition: 0.25s;
                background: #fff;
            }
            .zscu-card-main:hover {
                border-color: #1a5f3c;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(26,95,60,0.15);
            }
            .zscu-card-main.selected {
                border-color: #1a5f3c;
                background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
            }
            .zscu-card-main .icon { font-size: 36px; margin-bottom: 8px; display: block; }
            .zscu-card-main .label { font-weight: 700; color: #1f2937; font-size: 15px; }
            .zscu-card-main .desc { font-size: 11px; color: #6b7280; margin-top: 4px; }
            
            /* SUBTYPE Cards - SMALLER compact grid */
            .zscu-cards.sub-types {
                grid-template-columns: repeat(2, 1fr);
            }
            .zscu-card-sub {
                border: 2px solid #e5e7eb;
                border-radius: 8px;
                padding: 14px 10px;
                text-align: center;
                cursor: pointer;
                transition: 0.2s;
                background: #fff;
            }
            .zscu-card-sub:hover {
                border-color: #1a5f3c;
                background: #f9fafb;
            }
            .zscu-card-sub.selected {
                border-color: #1a5f3c;
                background: #f0fdf4;
            }
            .zscu-card-sub .icon { font-size: 24px; margin-bottom: 6px; display: block; }
            .zscu-card-sub .label { font-weight: 600; color: #374151; font-size: 12px; line-height: 1.3; word-wrap: break-word; }
            
            /* Form */
            .zscu-form-group { margin-bottom: 14px; }
            .zscu-label {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 12px;
                font-weight: 600;
                color: #374151;
                margin-bottom: 6px;
                text-transform: uppercase;
            }
            .zscu-input, .zscu-select {
                width: 100%;
                padding: 12px 14px;
                border: 2px solid #e5e7eb;
                border-radius: 8px;
                font-size: 14px;
                box-sizing: border-box;
                transition: 0.2s;
            }
            .zscu-input:focus, .zscu-select:focus {
                outline: none;
                border-color: #1a5f3c;
            }
            .zscu-select-wrap { position: relative; }
            .zscu-select-wrap::after {
                content: '▼';
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: #6b7280;
                font-size: 10px;
                pointer-events: none;
            }
            .zscu-select { padding-right: 30px; appearance: none; cursor: pointer; }
            
            /* Help */
            .zscu-help {
                display: inline-flex;
                width: 16px;
                height: 16px;
                border-radius: 50%;
                background: #e5e7eb;
                color: #666;
                font-size: 10px;
                align-items: center;
                justify-content: center;
                cursor: help;
                position: relative;
            }
            .zscu-help:hover { background: #1a5f3c; color: #fff; }
            .zscu-tooltip {
                position: absolute;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%);
                background: #1f2937;
                color: #fff;
                padding: 8px 12px;
                border-radius: 6px;
                font-size: 11px;
                width: 180px;
                margin-bottom: 8px;
                opacity: 0;
                visibility: hidden;
                transition: 0.2s;
                z-index: 100;
                line-height: 1.4;
                text-transform: none;
                font-weight: 400;
            }
            .zscu-tooltip::after {
                content: '';
                position: absolute;
                top: 100%;
                left: 50%;
                transform: translateX(-50%);
                border: 5px solid transparent;
                border-top-color: #1f2937;
            }
            .zscu-help:hover .zscu-tooltip { opacity: 1; visibility: visible; }
            
            /* Buttons */
            .zscu-btns {
                display: flex;
                gap: 10px;
                margin-top: 16px;
                flex-shrink: 0;
                padding-top: 12px;
                border-top: 1px solid #f3f4f6;
            }
            .zscu-btn {
                flex: 1;
                padding: 12px 16px;
                border: none;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: 0.25s;
            }
            .zscu-btn-primary {
                background: linear-gradient(135deg, #1a5f3c, #2e8b57);
                color: #fff;
            }
            .zscu-btn-primary:hover { transform: translateY(-1px); }
            .zscu-btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
            .zscu-btn-secondary { background: #f3f4f6; color: #4b5563; }
            .zscu-btn-secondary:hover { background: #e5e7eb; }
            
            /* Results */
            .zscu-result {
                text-align: center;
                padding: 20px;
                background: linear-gradient(135deg, #f8fafc, #f1f5f9);
                border-radius: 12px;
                margin-bottom: 16px;
            }
            .zscu-status {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 6px 12px;
                border-radius: 16px;
                font-size: 12px;
                font-weight: 600;
                margin-bottom: 10px;
            }
            .zscu-status.eligible { background: #dcfce7; color: #166534; }
            .zscu-status.not-eligible { background: #fee2e2; color: #991b1b; }
            .zscu-amount { font-size: 32px; font-weight: 800; color: #1a5f3c; }
            .zscu-amount-label { color: #6b7280; font-size: 13px; }
            .zscu-rate {
                display: inline-block;
                background: rgba(26,95,60,0.1);
                color: #1a5f3c;
                padding: 3px 10px;
                border-radius: 12px;
                font-size: 11px;
                font-weight: 600;
                margin-top: 6px;
            }
            
            /* Breakdown */
            .zscu-breakdown {
                background: #fff;
                border-radius: 10px;
                padding: 14px;
                margin-bottom: 14px;
                border: 1px solid #e5e7eb;
            }
            .zscu-breakdown-title {
                font-weight: 700;
                color: #1f2937;
                font-size: 13px;
                margin-bottom: 10px;
                padding-bottom: 8px;
                border-bottom: 1px solid #e5e7eb;
            }
            .zscu-row {
                display: flex;
                justify-content: space-between;
                padding: 6px 0;
                font-size: 13px;
            }
            .zscu-row:last-child { font-weight: 700; color: #1a5f3c; }
            .zscu-row-label { color: #4b5563; }
            .zscu-row-value { color: #1f2937; font-weight: 500; }
            .zscu-row-value.deduct { color: #dc2626; }
            
            /* CTA */
            .zscu-cta {
                background: linear-gradient(135deg, #1a5f3c, #2e8b57);
                color: #fff;
                padding: 18px;
                border-radius: 10px;
                text-align: center;
                margin-top: 16px;
            }
            .zscu-cta-title { font-size: 16px; font-weight: 700; margin-bottom: 6px; }
            .zscu-cta-text { font-size: 13px; opacity: 0.95; margin-bottom: 12px; }
            .zscu-cta-btn {
                display: inline-block;
                background: #fff;
                color: #1a5f3c;
                padding: 10px 24px;
                border-radius: 8px;
                font-weight: 700;
                text-decoration: none;
                font-size: 14px;
            }
            
            /* RTL */
            .zscu-rtl { direction: rtl; }
            .zscu-rtl .zscu-lang .flag { margin-right: 0; margin-left: 12px; }
            
            /* Mobile */
            @media (max-width: 480px) {
                .zscu-wrap {
                    margin: 10px;
                    padding: 16px;
                    min-height: 480px;
                    max-height: 580px;
                    border-radius: 12px;
                }
                .zscu-cards.sub-types { grid-template-columns: repeat(2, 1fr); }
                .zscu-card-main { padding: 16px 12px; }
                .zscu-card-main .icon { font-size: 30px; }
                .zscu-card-sub .icon { font-size: 20px; }
                .zscu-title { font-size: 18px; }
                .zscu-amount { font-size: 28px; }
            }
        </style>
        
        <!-- Progress -->
        <div class="zscu-progress">
            <div class="zscu-step-dot active" data-step="1"></div>
            <div class="zscu-step-line"></div>
            <div class="zscu-step-dot" data-step="2"></div>
            <div class="zscu-step-line"></div>
            <div class="zscu-step-dot" data-step="3"></div>
            <div class="zscu-step-line"></div>
            <div class="zscu-step-dot" data-step="4"></div>
        </div>
        
        <!-- Content Area -->
        <div class="zscu-content">
        
        <!-- Step 1: Language -->
        <div class="zscu-step active" data-step="1">
            <h2 class="zscu-title" id="zscu-t1">Select Language</h2>
            <p class="zscu-subtitle" id="zscu-s1">Choose your preferred language</p>
            
            <input type="text" class="zscu-search" id="zscu-search" placeholder="Search languages...">
            
            <div class="zscu-langs" id="zscu-langs">
                <div class="zscu-lang selected" data-lang="en" data-rtl="0"><span class="flag">🇬🇧</span><span class="name">English</span></div>
                <div class="zscu-lang" data-lang="ar" data-rtl="1"><span class="flag">🇸🇦</span><span class="name">العربية</span></div>
                <div class="zscu-lang" data-lang="id" data-rtl="0"><span class="flag">🇮🇩</span><span class="name">Bahasa Indonesia</span></div>
                <div class="zscu-lang" data-lang="tr" data-rtl="0"><span class="flag">🇹🇷</span><span class="name">Türkçe</span></div>
                <div class="zscu-lang" data-lang="ur" data-rtl="1"><span class="flag">🇵🇰</span><span class="name">اردو</span></div>
                <div class="zscu-lang" data-lang="de" data-rtl="0"><span class="flag">🇩🇪</span><span class="name">Deutsch</span></div>
                <div class="zscu-lang" data-lang="es" data-rtl="0"><span class="flag">🇪🇸</span><span class="name">Español</span></div>
                <div class="zscu-lang" data-lang="fr" data-rtl="0"><span class="flag">🇫🇷</span><span class="name">Français</span></div>
            </div>
        </div>
        
        <!-- Step 2: Type Selection -->
        <div class="zscu-step" data-step="2">
            <h2 class="zscu-title" id="zscu-t2">What to calculate?</h2>
            <p class="zscu-subtitle" id="zscu-s2">Select Zakat or Sadaqah</p>
            
            <div class="zscu-cards main-types" id="zscu-types">
                <div class="zscu-card-main" data-type="zakat">
                    <span class="icon">🕌</span>
                    <div class="label" id="zscu-l-z">Zakat</div>
                    <div class="desc" id="zscu-d-z">Obligatory</div>
                </div>
                <div class="zscu-card-main" data-type="sadaqah">
                    <span class="icon">💚</span>
                    <div class="label" id="zscu-l-s">Sadaqah</div>
                    <div class="desc" id="zscu-d-s">Voluntary</div>
                </div>
            </div>
            
            <div id="zscu-subtypes"></div>
        </div>
        
        <!-- Step 3: Details -->
        <div class="zscu-step" data-step="3">
            <h2 class="zscu-title" id="zscu-t3">Enter Details</h2>
            <p class="zscu-subtitle" id="zscu-s3">Fill in the required information</p>
            
            <div id="zscu-form"></div>
        </div>
        
        <!-- Step 4: Results -->
        <div class="zscu-step" data-step="4">
            <h2 class="zscu-title" id="zscu-t4">Your Results</h2>
            <div id="zscu-result"></div>
        </div>
        
        </div>
        
        <!-- Navigation -->
        <div class="zscu-btns">
            <button class="zscu-btn zscu-btn-secondary" id="zscu-btn-back" onclick="zscu.prev()" style="display:none">← Back</button>
            <button class="zscu-btn zscu-btn-primary" id="zscu-btn-next" onclick="zscu.next()">Continue →</button>
        </div>
    </div>
    
    <script>
    (function(){
        // Translations
        var t = {
            en: {
                s1_title: 'Select Language', s1_sub: 'Choose your preferred language',
                s2_title: 'What to calculate?', s2_sub: 'Select Zakat or Sadaqah',
                s3_title: 'Enter Details', s3_sub: 'Fill in the required information',
                s4_title: 'Your Results',
                zakat: 'Zakat', sadaqah: 'Sadaqah', z_desc: 'Obligatory', s_desc: 'Voluntary',
                z_maal: 'Cash', z_income: 'Income', z_fitr: 'Zakat Fitr', z_rental: 'Rental',
                z_invest: 'Invest', z_pension: 'Pension', z_jewelry: 'Jewelry', z_business: 'Business',
                z_agri: 'Agriculture', z_livestock: 'Livestock',
                s_general: 'General', s_orphan: 'Orphan Sponsorship', s_wells: 'Wells/Water', s_mosque: 'Mosque', s_meals: 'Meals for Poor', s_jariyah: 'Jariyah', s_education: 'Education', s_medical: 'Medical',
                l_material: 'Material', l_beneficiary: 'Beneficiary', l_frequency: 'Frequency',
                l_quantity: 'Quantity', l_amount: 'Amount',
                l_gold_price: 'Gold $/g', l_silver_price: 'Silver $/g',
                l_gold_weight: 'Gold (g)', l_silver_weight: 'Silver (g)', l_purity: 'Purity',
                l_cash: 'Cash Amount', l_income: 'Monthly Income', l_expenses: 'Monthly Expenses',
                l_rent: 'Annual Rent', l_portfolio: 'Portfolio', l_pension: 'Pension Value',
                l_business: 'Business Value', l_produce: 'Produce', l_animals: 'Animals',
                l_people: 'People', l_debts: 'Debts', l_project: 'Project',
                l_students: 'Students', l_patients: 'Patients',
                opt_gold: 'Gold', opt_silver: 'Silver', opt_cash: 'Cash',
                opt_orphans: 'Orphans', opt_families: 'Families', opt_refugees: 'Refugees',
                opt_poor: 'Poor', opt_students: 'Students', opt_patients: 'Patients',
                opt_once: 'One-time', opt_monthly: 'Monthly', opt_quarterly: 'Quarterly',
                tip_gold: 'Current market price per gram',
                tip_silver: 'Current silver price per gram',
                tip_debts: 'Deduct debts due within 12 months',
                tip_purity: 'Karat: 24K=99.9%, 22K=91.6%, 18K=75%',
                eligible: '✓ Eligible', not_eligible: '✗ Not Eligible',
                zakat_due: 'Zakat Due', sadaqah_due: 'Suggested Sadaqah',
                net: 'Net Wealth', nisab: 'Nisab', rate: 'Rate: 2.5%',
                cta_title: 'Fulfill Your Obligation', cta_text: 'Complete your Zakat now',
                cta_btn: 'Pay Zakat Now'
            },
            ar: {
                s1_title: 'اختر اللغة', s1_sub: 'اختر لغتك',
                s2_title: 'ماذا تريد حساب؟', s2_sub: 'زكاة أو صدقة',
                s3_title: 'أدخل التفاصيل', s3_sub: 'املأ المعلومات',
                s4_title: 'النتائج',
                zakat: 'الزكاة', sadaqah: 'الصدقة',
                z_maal: 'نقد', z_income: 'دخل', z_fitr: 'فطر', z_rental: 'إيجار',
                z_invest: 'استثمار', z_pension: 'معاش', z_jewelry: 'مجوهرات', z_business: 'تجارة',
                z_agri: 'زراعة', z_livestock: 'مواشي',
                s_general: 'عام', s_jariyah: 'جارية', s_education: 'تعليم', s_medical: 'طبي',
                l_material: 'المادة', l_beneficiary: 'المستفيد', l_frequency: 'التكرار',
                eligible: '✓ واجب', not_eligible: '✗ غير واجب',
                cta_btn: 'ادفع الآن'
            }
        };
        
        var zscu = {
            step: 1,
            lang: 'en',
            type: '',
            subtype: '',
            data: {},
            
            init: function() {
                var self = this;
                
                // Language search
                document.getElementById('zscu-search').addEventListener('input', function(e) {
                    var q = e.target.value.toLowerCase();
                    document.querySelectorAll('.zscu-lang').forEach(function(l) {
                        l.style.display = l.textContent.toLowerCase().includes(q) ? 'flex' : 'none';
                    });
                });
                
                // Language selection
                document.querySelectorAll('.zscu-lang').forEach(function(l) {
                    l.addEventListener('click', function() {
                        self.selectLang(this.dataset.lang, this.dataset.rtl === '1');
                    });
                });
                
                // Type selection
                document.querySelectorAll('.zscu-card-main').forEach(function(c) {
                    c.addEventListener('click', function() {
                        self.selectType(this.dataset.type);
                    });
                });
            },
            
            selectLang: function(langCode, isRtl) {
                this.lang = langCode;
                document.querySelectorAll('.zscu-lang').forEach(function(l) {
                    l.classList.toggle('selected', l.dataset.lang === langCode);
                });
                document.getElementById('<?php echo esc_js($id); ?>').classList.toggle('zscu-rtl', isRtl);
                this.updateText();
            },
            
            updateText: function() {
                var x = t[this.lang] || t.en;
                var titles = {
                    'zscu-t1': x.s1_title, 'zscu-s1': x.s1_sub,
                    'zscu-t2': x.s2_title, 'zscu-s2': x.s2_sub,
                    'zscu-t3': x.s3_title, 'zscu-s3': x.s3_sub,
                    'zscu-t4': x.s4_title
                };
                for (var id in titles) {
                    var el = document.getElementById(id);
                    if (el) el.textContent = titles[id];
                }
                document.getElementById('zscu-l-z').textContent = x.zakat;
                document.getElementById('zscu-d-z').textContent = x.z_desc || 'Obligatory';
                document.getElementById('zscu-l-s').textContent = x.sadaqah;
                document.getElementById('zscu-d-s').textContent = x.s_desc || 'Voluntary';
            },
            
            selectType: function(type) {
                this.type = type;
                this.subtype = '';
                
                // Update main card selection
                document.querySelectorAll('.zscu-card-main').forEach(function(c) {
                    c.classList.toggle('selected', c.dataset.type === type);
                });
                
                // Render subtypes with SMALL icons
                this.renderSubtypes();
            },
            
            renderSubtypes: function() {
                var x = t[this.lang] || t.en;
                var container = document.getElementById('zscu-subtypes');
                var html = '<div class="zscu-cards sub-types">';
                
                if (this.type === 'zakat') {
                    var items = [
                        ['z_maal','💰',x.z_maal],['z_income','💼',x.z_income],['z_fitr','🌙',x.z_fitr],
                        ['z_rental','🏠',x.z_rental],['z_invest','📈',x.z_invest],['z_pension','🎓',x.z_pension],
                        ['z_jewelry','💍',x.z_jewelry],['z_business','🏪',x.z_business],['z_agri','🌾',x.z_agri],['z_livestock','🐄',x.z_livestock]
                    ];
                } else {
                    var items = [
                        ['s_general','💚',x.s_general],['s_orphan','👶',x.s_orphan],['s_wells','💧',x.s_wells],
                        ['s_mosque','🕌',x.s_mosque],['s_meals','🍽️',x.s_meals],['s_education','📚',x.s_education],
                        ['s_medical','🏥',x.s_medical]
                    ];
                }
                
                var self = this;
                items.forEach(function(item) {
                    html += '<div class="zscu-card-sub" data-subtype="'+item[0]+'">' +
                            '<span class="icon">'+item[1]+'</span>' +
                            '<div class="label">'+item[2]+'</div></div>';
                });
                
                html += '</div>';
                container.innerHTML = html;
                
                // Add click handlers
                container.querySelectorAll('.zscu-card-sub').forEach(function(c) {
                    c.addEventListener('click', function() {
                        self.selectSubtype(this.dataset.subtype);
                    });
                });
            },
            
            selectSubtype: function(subtype) {
                this.subtype = subtype;
                document.querySelectorAll('.zscu-card-sub').forEach(function(c) {
                    c.classList.toggle('selected', c.dataset.subtype === subtype);
                });
                document.getElementById('zscu-btn-next').disabled = false;
            },
            
            next: function() {
                if (this.step < 4) {
                    this.goTo(this.step + 1);
                }
            },
            
            prev: function() {
                if (this.step > 1) {
                    this.goTo(this.step - 1);
                }
            },
            
            goTo: function(stepNum) {
                // Hide current
                document.querySelectorAll('.zscu-step').forEach(function(s) {
                    s.classList.remove('active');
                });
                
                // Show new
                document.querySelector('.zscu-step[data-step="'+stepNum+'"]').classList.add('active');
                
                // Update progress
                var dots = document.querySelectorAll('.zscu-step-dot');
                var lines = document.querySelectorAll('.zscu-step-line');
                dots.forEach(function(d, i) {
                    d.classList.remove('active', 'completed');
                    if (i+1 === stepNum) d.classList.add('active');
                    else if (i+1 < stepNum) d.classList.add('completed');
                });
                lines.forEach(function(l, i) {
                    l.classList.toggle('completed', i < stepNum - 1);
                });
                
                // Update buttons
                document.getElementById('zscu-btn-back').style.display = stepNum > 1 ? 'block' : 'none';
                document.getElementById('zscu-btn-next').textContent = stepNum === 3 ? '🧮 Calculate' : 'Continue →';
                document.getElementById('zscu-btn-next').onclick = stepNum === 3 ? function(){zscu.calculate();} : function(){zscu.next();};
                
                this.step = stepNum;
                
                // Step 3: show form
                if (stepNum === 3) this.renderForm();
                // Step 4: show results (already done in calculate)
            },
            
            renderForm: function() {
                var x = t[this.lang] || t.en;
                var container = document.getElementById('zscu-form');
                var s = this.subtype;
                var html = '';
                
                // Material for Jewelry/Maal
                if (s === 'z_jewelry' || s === 'z_maal') {
                    html += this.select('material', x.l_material, [
                        {value:'',label:'-- Select --'},
                        {value:'gold',label:x.opt_gold},
                        {value:'silver',label:x.opt_silver},
                        {value:'cash',label:x.opt_cash}
                    ], x.tip_gold);
                    html += '<div id="material-fields"></div>';
                }
                
                // Sadaqah beneficiary/frequency
                if (this.type === 'sadaqah') {
                    html += this.select('beneficiary', x.l_beneficiary, [
                        {value:'',label:'-- Select --'},
                        {value:'orphans',label:x.opt_orphans},
                        {value:'families',label:x.opt_families},
                        {value:'refugees',label:x.opt_refugees},
                        {value:'poor',label:x.opt_poor},
                        {value:'students',label:x.opt_students},
                        {value:'patients',label:x.opt_patients}
                    ]);
                    html += this.select('frequency', x.l_frequency, [
                        {value:'once',label:x.opt_once},
                        {value:'monthly',label:x.opt_monthly},
                        {value:'quarterly',label:x.opt_quarterly}
                    ]);
                    html += this.input('quantity', x.l_quantity, 'number', '1', 'people');
                    html += this.input('amount', x.l_amount, 'number', '', '$');
                }
                
                // Specific fields
                if (s === 'z_income') {
                    html += this.input('income', x.l_income, 'number', '', '$/mo');
                    html += this.input('expenses', x.l_expenses, 'number', '', '$/mo');
                }
                if (s === 'z_fitr' || s === 's_general') {
                    html += this.input('people', x.l_people, 'number', '1', 'people');
                }
                if (s === 'z_rental') {
                    html += this.input('rent', x.l_rent, 'number', '', '$/yr');
                }
                if (s === 'z_invest' || s === 'z_maal' || s === 'z_jewelry') {
                    html += this.input('gold_price', x.l_gold_price, 'number', '', '$/g', x.tip_gold);
                    html += this.input('silver_price', x.l_silver_price, 'number', '', '$/g', x.tip_silver);
                }
                if (s === 'z_business') {
                    html += this.input('business', x.l_business, 'number', '', '$');
                }
                if (s === 'z_pension') {
                    html += this.input('pension', x.l_pension, 'number', '', '$');
                }
                if (s === 'z_agri') {
                    html += this.input('produce', x.l_produce, 'number', '', '$');
                }
                if (s === 'z_livestock') {
                    html += this.input('animals', x.l_animals, 'number', '', 'animals');
                }
                if (s === 's_jariyah') {
                    html += this.input('project', x.l_project, 'number', '', '$');
                }
                if (s === 's_education') {
                    html += this.input('students', x.l_students, 'number', '', 'students');
                    html += this.input('amount', x.l_amount, 'number', '', '$');
                }
                if (s === 's_medical') {
                    html += this.input('patients', x.l_patients, 'number', '', 'patients');
                    html += this.input('amount', x.l_amount, 'number', '', '$');
                }
                
                // Debts for wealth types
                if (['z_maal','z_invest','z_jewelry','z_business'].includes(s)) {
                    html += this.input('debts', x.l_debts, 'number', '', '$', x.tip_debts);
                }
                
                container.innerHTML = html;
                
                // Material change handler
                var matSelect = container.querySelector('#material');
                if (matSelect) {
                    matSelect.addEventListener('change', function() {
                        zscu.renderMaterialFields(this.value);
                    });
                }
            },
            
            input: function(id, label, type, value, suffix, tooltip) {
                var html = '<div class="zscu-form-group">';
                html += '<label class="zscu-label">'+label;
                if (tooltip) html += '<span class="zscu-help">?<span class="zscu-tooltip">'+tooltip+'</span></span>';
                html += '</label>';
                html += '<input type="'+type+'" class="zscu-input" id="'+id+'" value="'+value+'" placeholder="0">';
                html += '</div>';
                return html;
            },
            
            select: function(id, label, options, tooltip) {
                var html = '<div class="zscu-form-group">';
                html += '<label class="zscu-label">'+label;
                if (tooltip) html += '<span class="zscu-help">?<span class="zscu-tooltip">'+tooltip+'</span></span>';
                html += '</label>';
                html += '<div class="zscu-select-wrap">';
                html += '<select class="zscu-select" id="'+id+'">';
                options.forEach(function(opt) {
                    html += '<option value="'+opt.value+'">'+opt.label+'</option>';
                });
                html += '</select></div></div>';
                return html;
            },
            
            renderMaterialFields: function(material) {
                var x = t[this.lang] || t.en;
                var container = document.getElementById('material-fields');
                var html = '';
                
                if (material === 'gold') {
                    html += this.input('gold_weight', x.l_gold_weight, 'number', '', 'g');
                    html += this.select('purity', x.l_purity, [
                        {value:'24',label:'24K'}, {value:'22',label:'22K'}, {value:'21',label:'21K'}, {value:'18',label:'18K'}
                    ], x.tip_purity);
                } else if (material === 'silver') {
                    html += this.input('silver_weight', x.l_silver_weight, 'number', '', 'g');
                } else if (material === 'cash') {
                    html += this.input('cash', x.l_cash, 'number', '', '$');
                }
                
                container.innerHTML = html;
            },
            
            calculate: function() {
                var x = t[this.lang] || t.en;
                var s = this.subtype;
                var result = { amount: 0, nisab: 0, net: 0, eligible: false, rows: [], isZakat: this.type === 'zakat' };
                
                var getVal = function(id) {
                    var el = document.getElementById(id);
                    return el ? parseFloat(el.value) || 0 : 0;
                };
                
                // Calculate based on type
                if (s === 'z_maal' || s === 'z_jewelry') {
                    var mat = document.getElementById('material') ? document.getElementById('material').value : '';
                    var gp = getVal('gold_price');
                    var sp = getVal('silver_price');
                    var wealth = 0;
                    
                    if (mat === 'gold') {
                        var gw = getVal('gold_weight');
                        var purity = getVal('purity') || 24;
                        wealth = (gw * purity / 24) * gp;
                        result.rows.push({l: x.l_gold_weight, v: gw+'g × '+purity+'K'});
                    } else if (mat === 'silver') {
                        var sw = getVal('silver_weight');
                        wealth = sw * sp;
                        result.rows.push({l: x.l_silver_weight, v: sw+'g'});
                    } else if (mat === 'cash') {
                        wealth = getVal('cash');
                        result.rows.push({l: x.l_cash, v: '$'+wealth});
                    }
                    
                    var debts = getVal('debts');
                    result.nisab = Math.min(85*gp, 595*sp);
                    result.net = Math.max(0, wealth - debts);
                    result.eligible = result.net >= result.nisab && result.nisab > 0;
                    result.amount = result.eligible ? result.net * 0.025 : 0;
                    
                    if (debts > 0) result.rows.push({l: x.l_debts, v: '$'+debts, d: true});
                    result.rows.push({l: x.net, v: '$'+result.net.toFixed(2)});
                }
                else if (s === 'z_income') {
                    var income = getVal('income');
                    var exp = getVal('expenses');
                    result.net = (income - exp) * 12;
                    result.amount = Math.max(0, result.net) * 0.025;
                    result.eligible = result.amount > 0;
                    result.rows.push({l: x.l_income, v: '$'+income+'/mo'});
                    if (exp > 0) result.rows.push({l: x.l_expenses, v: '$'+exp+'/mo', d: true});
                }
                else if (s === 'z_fitr') {
                    var people = getVal('people') || 1;
                    result.amount = people * 12;
                    result.eligible = true;
                    result.rows.push({l: x.l_people, v: people});
                }
                else if (s === 'z_rental') {
                    var rent = getVal('rent');
                    result.net = rent;
                    result.amount = rent * 0.025;
                    result.eligible = rent > 0;
                    result.rows.push({l: x.l_rent, v: '$'+rent.toFixed(2)});
                }
                else if (s === 'z_invest') {
                    var port = getVal('portfolio');
                    var gp = getVal('gold_price');
                    var sp = getVal('silver_price');
                    var debts = getVal('debts');
                    result.nisab = Math.min(85*gp, 595*sp);
                    result.net = port - debts;
                    result.eligible = result.net >= result.nisab && result.nisab > 0;
                    result.amount = result.eligible ? result.net * 0.025 : 0;
                    result.rows.push({l: x.l_portfolio, v: '$'+port});
                    if (debts > 0) result.rows.push({l: x.l_debts, v: '$'+debts, d: true});
                }
                else if (s === 'z_pension') {
                    var pen = getVal('pension');
                    result.amount = pen * 0.025;
                    result.eligible = pen > 0;
                    result.rows.push({l: x.l_pension, v: '$'+pen});
                }
                else if (s === 'z_business') {
                    var biz = getVal('business');
                    var debts = getVal('debts');
                    result.net = biz - debts;
                    result.amount = Math.max(0, result.net) * 0.025;
                    result.eligible = result.amount > 0;
                    result.rows.push({l: x.l_business, v: '$'+biz});
                    if (debts > 0) result.rows.push({l: x.l_debts, v: '$'+debts, d: true});
                }
                else if (s === 'z_agri') {
                    var prod = getVal('produce');
                    result.amount = prod * 0.1;
                    result.eligible = prod > 0;
                    result.rows.push({l: x.l_produce, v: '$'+prod});
                }
                else if (s === 'z_livestock') {
                    var animals = getVal('animals');
                    result.eligible = animals >= 40;
                    result.amount = result.eligible ? Math.floor(animals/40)*100 : 0;
                    result.rows.push({l: x.l_animals, v: animals});
                }
                else {
                    // Sadaqah
                    var qty = getVal('quantity') || 1;
                    var amt = getVal('amount');
                    result.eligible = true;
                    result.amount = qty * amt;
                    result.rows.push({l: x.l_quantity, v: qty});
                    result.rows.push({l: x.l_amount, v: '$'+amt.toFixed(2)});
                }
                
                if (result.nisab > 0) result.rows.push({l: x.nisab, v: '$'+result.nisab.toFixed(2)});
                
                this.renderResults(result, x);
                this.goTo(4);
            },
            
            renderResults: function(r, x) {
                var container = document.getElementById('zscu-result');
                var html = '';
                
                html += '<div class="zscu-result">';
                html += '<div class="zscu-status '+(r.eligible?'eligible':'not-eligible')+'">'+(r.eligible?x.eligible:x.not_eligible)+'</div>';
                html += '<div class="zscu-amount">$'+r.amount.toFixed(2)+'</div>';
                html += '<div class="zscu-amount-label">'+(r.isZakat?x.zakat_due:x.sadaqah_due)+'</div>';
                if (r.isZakat) html += '<div class="zscu-rate">'+x.rate+'</div>';
                html += '</div>';
                
                html += '<div class="zscu-breakdown">';
                html += '<div class="zscu-breakdown-title">📊 Calculation</div>';
                r.rows.forEach(function(row) {
                    html += '<div class="zscu-row">';
                    html += '<span class="zscu-row-label">'+row.l+'</span>';
                    html += '<span class="zscu-row-value'+(row.d?' deduct':'')+'">'+row.v+'</span>';
                    html += '</div>';
                });
                html += '</div>';
                
                if (this.showCta && r.eligible) {
                    html += '<div class="zscu-cta">';
                    html += '<div class="zscu-cta-title">🤲 '+x.cta_title+'</div>';
                    html += '<div class="zscu-cta-text">'+x.cta_text+'</div>';
                    html += '<a href="'+this.ctaUrl+'" target="_blank" class="zscu-cta-btn">'+x.cta_btn+' →</a>';
                    html += '</div>';
                }
                
                container.innerHTML = html;
            }
        };
        
        zscu.showCta = <?php echo $show_cta ? 'true' : 'false'; ?>;
        zscu.ctaUrl = '<?php echo esc_js($cta_url); ?>';
        zscu.init();
        window.zscu = zscu;
    })();
    </script>
    <?php
    return ob_get_clean();
}

// Admin
add_action('admin_menu', function() {
    add_options_page('Zakat Ultimate', 'Zakat Ultimate', 'manage_options', 'zakat-ultimate', 'zscu_admin');
});

function zscu_admin() {
    ?>
    <div class="wrap">
        <h1>🕌 Zakat Ultimate v3.1</h1>
        <div class="notice notice-info">
            <p><code>[zakat_ultimate]</code> - Standard (no CTA)</p>
            <p><code>[zakat_ultimate_pro]</code> - With CTA</p>
        </div>
        <h3>Features:</h3>
        <ul>
            <li>✅ Fixed height container - no jumping</li>
            <li>✅ Smaller subtype icons for better UX</li>
            <li>✅ 10 Zakat types + 4 Sadaqah types</li>
            <li>✅ Material dropdowns for Jewelry</li>
            <li>✅ Beneficiary/frequency for Sadaqah</li>
            <li>✅ Help tooltips</li>
            <li>✅ Correct Islamic calculations</li>
        </ul>
    </div>
    <?php
}
