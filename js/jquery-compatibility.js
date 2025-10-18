/**
 * jQuery Compatibility Fix
 * แก้ไขปัญหา jQuery เวอร์ชันเก่าที่ใช้กับ jQuery 3.x
 */

// เพิ่ม andSelf() กลับมาเพื่อให้ plugin เก่าทำงานได้
if (typeof jQuery !== 'undefined' && !jQuery.fn.andSelf) {
    jQuery.fn.andSelf = jQuery.fn.addBack;
    console.log('jQuery compatibility: Added andSelf() as alias for addBack()');
}

// แก้ไข .size() ที่ถูกลบใน jQuery 3.x
if (typeof jQuery !== 'undefined' && !jQuery.fn.size) {
    jQuery.fn.size = function() {
        return this.length;
    };
    console.log('jQuery compatibility: Added size() method');
}

// แก้ไข .load() event ที่ถูกลบใน jQuery 3.x
if (typeof jQuery !== 'undefined' && !jQuery.fn.load) {
    jQuery.fn.load = function(fn) {
        return this.on('load', fn);
    };
}

console.log('jQuery Compatibility Fix loaded');
