# فصل اليوزر العادي عن الـ Agents

## فهمي للمطلوب

- اليوزر الذي يسجل من واجهة الموقع ويدخل على `/account/dashboard` هو عميل/مستخدم عادي، وليس Agent.
- الـ Agents هم الحسابات التي يضيفها الأدمن من لوحة التحكم، وهم فقط الذين يجب ظهورهم كاختيار عند الموافقة على العقار.
- صفحة الأدمن `/admin/real-estate/accounts` مطلوب أن تعرض اليوزر العادي المسجل من الموقع.
- العقارات التي يرفعها اليوزر العادي من حسابه تظل منسوبة له أثناء المراجعة.
- عند موافقة الأدمن على العقار، يختار Agent فقط من القائمة، وبعد الموافقة ينتقل العقار من حالة المراجعة إلى قائمة العقارات المعتمدة ويصبح منسوبا للـ Agent المختار.
- صفحة `/admin/real-estate/properties/pending` بالشكل الحالي غير مرغوبة كقسم منفصل؛ المطلوب أن عقارات اليوزر العادي تكون ضمن تدفق اليوزر العادي، وعند الموافقة تتحول لقائمة العقارات الرئيسية.

## الوضع الحالي في الكود

- النظام يستخدم جدول واحد اسمه `re_accounts` لكل من:
  - المستخدمين المسجلين من الواجهة.
  - الـ Agents المضافين من الأدمن.
- موديل `Account` هو نفسه المستخدم للحسابين.
- العقارات في `re_properties` مربوطة بمالك من خلال:
  - `author_id`
  - `author_type`
- عند رفع عقار من واجهة المستخدم، الكود يحفظ:
  - `author_id = id` الخاص بالمستخدم الحالي.
  - `author_type = Botble\RealEstate\Models\Account`.
  - `moderation_status = pending` إذا لم يكن Draft.
- عند موافقة الأدمن في `PropertyController@approve`، الكود الحالي يطلب `author_id` ويغير مالك العقار للحساب المختار ثم يجعل `moderation_status = approved`.
- المشكلة الأساسية أن `re_accounts` لا يميز حاليا بين User عادي وAgent، لذلك القوائم والاختيارات تعرض الاثنين معا.

## القرار الفني المقترح

أفضل حل بأقل مخاطرة هو إضافة نوع للحساب داخل نفس جدول `re_accounts` بدلا من إنشاء جدول جديد.

الأنواع المقترحة:

- `user`: أي حساب يسجل من الواجهة `/account/register` ويدخل `/account/dashboard`.
- `agent`: أي حساب يضيفه الأدمن من لوحة التحكم كوسيط عقاري.

سبب اختيار هذا الحل:

- العلاقات الحالية للعقارات، البروفايل، الصور، المدفوعات، النشاطات، والـ slugs كلها مبنية على `Account`.
- فصل جدول جديد سيحتاج تعديلات أكبر في العلاقات والـ auth والـ slugs والواجهات.
- إضافة عمود `account_type` تحقق الفصل المطلوب بدون كسر النظام الحالي.

## خطوات التنفيذ المقترحة

1. إضافة migration جديدة لجدول `re_accounts`:
   - إضافة عمود `account_type` بقيم افتراضية `user`.
   - عمل index على العمود.

2. تحديث موديل `Account`:
   - إضافة `account_type` إلى `$fillable`.
   - إضافة cast نصي أو enum بسيط.
   - إضافة scopes مساعدة:
     - `agents()`
     - `regularUsers()`
   - إضافة helpers:
     - `isAgent()`
     - `isRegularUser()`

3. تحديث التسجيل من الواجهة:
   - في `RegisterController@create` يتم حفظ الحساب الجديد كـ `account_type = user`.

4. تحديث إنشاء الحساب من الأدمن:
   - صفحة `/admin/real-estate/accounts/create` ستنشئ `account_type = user`.
   - صفحة Agents المنفصلة ستنشئ `account_type = agent`.

5. تحديث صفحة `/admin/real-estate/accounts`:
   - تعديل `AccountTable` لتعرض فقط الحسابات ذات `account_type = user`.
   - عنوان الصفحة والترجمات ممكن تظل كما هي مبدئيا أو تتحول لاحقا إلى "Users" حسب رغبتك.

6. توفير إدارة للـ Agents:
   - إضافة route منفصل مثل `/admin/real-estate/agents` يعرض `account_type = agent`.
   - إنشاء Agents من الصفحة المنفصلة فقط.

7. تحديث اختيار مالك العقار في فورم العقار:
   - في `PropertyForm` يتم تحميل `$accounts` من الحسابات التي `account_type = agent` فقط.
   - بذلك عند إنشاء/تعديل عقار من الأدمن، قائمة الحسابات لا تعرض إلا الـ Agents.

8. تحديث موافقة العقار:
   - في `PropertyController@approve` يتم التحقق أن `author_id` المختار موجود في `re_accounts` ونوعه `agent`.
   - إذا اختار الأدمن حساب User عادي بالتحايل أو request يدوي، يتم رفض الطلب validation.
   - بعد الموافقة:
     - `author_id` يتحول للـ Agent.
     - `moderation_status` يتحول إلى `approved`.

9. تعديل صفحة pending:
   - إذا المطلوب إلغاء الرابط من القائمة فقط: نخفي/نزيل menu item الخاص بـ pending.
   - إذا المطلوب إلغاء الصفحة تماما: نلغي route أو نخليها redirect لقائمة العقارات مع فلتر pending.
   - الأفضل كبداية: إزالة ظهورها من المنيو وترك route داخليا حتى لا نكسر أي روابط قديمة.

10. ترحيل البيانات القديمة:
   - لا توجد قائمة IDs/أسماء مؤكدة للـ Agents الحاليين.
   - لذلك لن يتم تخمين تصنيف الحسابات القديمة كـ Agents.
   - القيمة الافتراضية ستكون `user`، وأي Agent قديم يحتاج نقله يدويا أو من خلال سكريبت لاحق بعد تحديده.

## ملفات متوقع تعديلها

- `platform/plugins/real-estate/database/migrations/...add_account_type_to_re_accounts_table.php`
- `platform/plugins/real-estate/src/Models/Account.php`
- `platform/plugins/real-estate/src/Http/Controllers/Fronts/RegisterController.php`
- `platform/plugins/real-estate/src/Http/Controllers/AccountController.php`
- `platform/plugins/real-estate/src/Tables/AccountTable.php`
- `platform/plugins/real-estate/src/Forms/PropertyForm.php`
- `platform/plugins/real-estate/src/Http/Controllers/PropertyController.php`
- ملف routes أو service provider الخاص بمنيو الأدمن إذا قررنا إضافة صفحة Agents أو إخفاء pending.

## أسئلة قبل التنفيذ

1. هل أي حساب يضاف من `/admin/real-estate/accounts/create` نعتبره Agent تلقائيا؟ الإجابة: لا.
2. هل تريد صفحة منفصلة للـ Agents في الأدمن؟ الإجابة: نعم، صفحة منفصلة.
3. هل توجد أسماء/IDs للـ Agents الحاليين؟ الإجابة: لا، لذلك لن نصنف القديم بالتخمين.
