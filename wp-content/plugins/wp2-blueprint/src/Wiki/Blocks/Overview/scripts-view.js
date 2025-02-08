import 'blockstudio/fullpage.js@4.0.33/dist/fullpage.min.css';
import fullpageJs from 'blockstudio/fullpage.js@4.0.33';


new fullpageJs('#WP2Overview', {

    licenseKey: typeof wp2FullpageConfig !== 'undefined' ? wp2FullpageConfig.licenseKey : '',

    menu: '#WP2OverviewMenu',
    fixedElements: ['#WP2WP2OverviewMenu'],

    paddingTop: '100px',
    paddingBottom: '100px',
    sectionSelector: '.wp2-section',

    fitToSection: false,

    verticalCentered: false,

    autoScrolling: false,

    css3: false,

    loopHorizontal: false,
    loopVertical: false,

    navigation: true,
    navigationPosition: 'bottom',

    slidesNavigation: true,

    keyboardScrolling: true,
    animateAnchor: true,
    recordHistory: true,

    responsiveWidth: 1500,
    responsiveSlides: false,

    observer: true,

    controlArrows: false,

    scrollOverflow: false,

    credits: false,

});



