(function () {
  var Container = window.jQuery || window.Zepto || window;
  if (!Container.Velocity || !Container.Velocity.Utilities) {
    console.log("Velocity UI Pack: Velocity must first be loaded. Aborting.");
    return;
  }
  var effects = {
    "callout.bounce": {
      defaultDuration: 550,
      calls: [
        [{ translateY: -30 }, 0.25],
        [{ translateY: 0 }, 0.125],
        [{ translateY: -15 }, 0.125],
        [{ translateY: 0 }, 0.25],
      ],
    },
    "callout.shake": {
      defaultDuration: 800,
      calls: [
        [{ translateX: -10 }, 0.125],
        [{ translateX: 10 }, 0.125, 3],
        [{ translateX: 0 }, 0.125],
      ],
    },
    "callout.flash": {
      defaultDuration: 1100,
      calls: [
        [{ opacity: [0, "swing", 1] }, 0.25],
        [{ opacity: [1, "swing"] }, 0.25],
        [{ opacity: [0, "swing"] }, 0.25],
        [{ opacity: [1, "swing"] }, 0.25],
      ],
    },
    "callout.pulse": {
      defaultDuration: 900,
      calls: [
        [{ scaleX: 1.1, scaleY: 1.1 }, 0.5],
        [{ scaleX: 1, scaleY: 1 }, 0.5],
      ],
    },
    "callout.swing": {
      defaultDuration: 950,
      calls: [
        [{ rotateZ: 15 }, 0.2],
        [{ rotateZ: -10 }, 0.2],
        [{ rotateZ: 5 }, 0.2],
        [{ rotateZ: -5 }, 0.2],
        [{ rotateZ: 0 }, 0.2],
      ],
    },
    "callout.tada": {
      defaultDuration: 1e3,
      calls: [
        [{ scaleX: 0.9, scaleY: 0.9, rotateZ: -3 }, 0.1],
        [{ scaleX: 1.1, scaleY: 1.1, rotateZ: 3 }, 0.1],
        [{ scaleX: 1.1, scaleY: 1.1, rotateZ: -3 }, 0.1, 3],
        [{ scaleX: 1, scaleY: 1, rotateZ: 0 }, 0.2],
      ],
    },
    "transition.flipXIn": {
      defaultDuration: 700,
      calls: [
        [
          {
            opacity: [1, 0],
            transformPerspective: [800, 800],
            rotateY: [0, -55],
          },
        ],
      ],
      reset: { transformPerspective: 0 },
    },
    "transition.flipXOut": {
      defaultDuration: 700,
      calls: [
        [{ opacity: [0, 1], transformPerspective: [800, 800], rotateY: 55 }],
      ],
      reset: { opacity: [1, 1], transformPerspective: 0, rotateY: 0 },
    },
    "transition.flipYIn": {
      defaultDuration: 700,
      calls: [
        [
          {
            opacity: [1, 0],
            transformPerspective: [800, 800],
            rotateX: [0, -35],
          },
        ],
      ],
      reset: { transformPerspective: 0 },
    },
    "transition.flipYOut": {
      defaultDuration: 700,
      calls: [
        [{ opacity: [0, 1], transformPerspective: [800, 800], rotateX: 25 }],
      ],
      reset: { opacity: [1, 1], transformPerspective: 0, rotateX: 0 },
    },
    "transition.flipBounceXIn": {
      defaultDuration: 900,
      calls: [
        [
          {
            opacity: [0.725, 0],
            transformPerspective: [400, 400],
            rotateY: [-10, 90],
          },
          0.5,
        ],
        [{ opacity: 0.8, rotateY: 10 }, 0.25],
        [{ opacity: 1, rotateY: 0 }, 0.25],
      ],
      reset: { transformPerspective: 0 },
    },
    "transition.flipBounceXOut": {
      defaultDuration: 800,
      calls: [
        [
          { opacity: [0.9, 1], transformPerspective: [400, 400], rotateY: -10 },
          0.5,
        ],
        [{ opacity: 0, rotateY: 90 }, 0.5],
      ],
      reset: { opacity: [1, 1], transformPerspective: 0, rotateY: 0 },
    },
    "transition.flipBounceYIn": {
      defaultDuration: 850,
      calls: [
        [
          {
            opacity: [0.725, 0],
            transformPerspective: [400, 400],
            rotateX: [-10, 90],
          },
          0.5,
        ],
        [{ opacity: 0.8, rotateX: 10 }, 0.25],
        [{ opacity: 1, rotateX: 0 }, 0.25],
      ],
      reset: { transformPerspective: 0 },
    },
    "transition.flipBounceYOut": {
      defaultDuration: 800,
      calls: [
        [
          { opacity: [0.9, 1], transformPerspective: [400, 400], rotateX: -15 },
          0.5,
        ],
        [{ opacity: 0, rotateX: 90 }, 0.5],
      ],
      reset: { opacity: [1, 1], transformPerspective: 0, rotateX: 0 },
    },
    "transition.swoopIn": {
      defaultDuration: 850,
      calls: [
        [
          {
            opacity: [1, 0],
            transformOriginX: ["100%", "25%"],
            transformOriginY: ["100%", "100%"],
            transformOriginZ: [0, 0],
            scaleX: [1, 0],
            scaleY: [1, 0],
            translateX: [0, -700],
            translateZ: 0,
          },
        ],
      ],
      reset: { transformOriginX: "50%", transformOriginY: "50%" },
    },
    "transition.swoopOut": {
      defaultDuration: 850,
      calls: [
        [
          {
            opacity: [0, 1],
            transformOriginX: ["25%", "100%"],
            transformOriginY: ["100%", "100%"],
            transformOriginZ: [0, 0],
            scaleX: 0,
            scaleY: 0,
            translateX: -700,
            translateZ: 0,
          },
        ],
      ],
      reset: {
        opacity: [1, 1],
        transformOriginX: "50%",
        transformOriginY: "50%",
        scaleX: 1,
        scaleY: 1,
        translateX: 0,
      },
    },
    "transition.whirlIn": {
      defaultDuration: 1e3,
      calls: [
        [
          {
            opacity: [1, 0],
            transformOriginX: ["50%", "50%"],
            transformOriginY: ["50%", "50%"],
            transformOriginZ: [0, 0],
            scaleX: [1, 0],
            scaleY: [1, 0],
            rotateY: [0, 180],
          },
        ],
      ],
    },
    "transition.whirlOut": {
      defaultDuration: 1e3,
      calls: [
        [
          {
            opacity: [0, 1],
            transformOriginX: ["50%", "50%"],
            transformOriginY: ["50%", "50%"],
            transformOriginZ: [0, 0],
            scaleX: 0,
            scaleY: 0,
            rotateY: 180,
          },
        ],
      ],
      reset: { opacity: [1, 1], scaleX: 1, scaleY: 1, rotateY: 0 },
    },
    "transition.shrinkIn": {
      defaultDuration: 700,
      calls: [
        [
          {
            opacity: [1, 0],
            transformOriginX: ["50%", "50%"],
            transformOriginY: ["50%", "50%"],
            transformOriginZ: [0, 0],
            scaleX: [1, 1.625],
            scaleY: [1, 1.625],
            translateZ: 0,
          },
        ],
      ],
    },
    "transition.shrinkOut": {
      defaultDuration: 700,
      calls: [
        [
          {
            opacity: [0, 1],
            transformOriginX: ["50%", "50%"],
            transformOriginY: ["50%", "50%"],
            transformOriginZ: [0, 0],
            scaleX: 1.35,
            scaleY: 1.35,
            translateZ: 0,
          },
        ],
      ],
      reset: { opacity: [1, 1], scaleX: 1, scaleY: 1 },
    },
    "transition.expandIn": {
      defaultDuration: 700,
      calls: [
        [
          {
            opacity: [1, 0],
            transformOriginX: ["50%", "50%"],
            transformOriginY: ["50%", "50%"],
            transformOriginZ: [0, 0],
            scaleX: [1, 0.625],
            scaleY: [1, 0.625],
            translateZ: 0,
          },
        ],
      ],
    },
    "transition.expandOut": {
      defaultDuration: 700,
      calls: [
        [
          {
            opacity: [0, 1],
            transformOriginX: ["50%", "50%"],
            transformOriginY: ["50%", "50%"],
            transformOriginZ: [0, 0],
            scaleX: 0.5,
            scaleY: 0.5,
            translateZ: 0,
          },
        ],
      ],
      reset: { opacity: [1, 1], scaleX: 1, scaleY: 1 },
    },
    "transition.bounceIn": {
      defaultDuration: 1e3,
      calls: [
        [{ opacity: [1, 0], scaleX: [1.05, 0.3], scaleY: [1.05, 0.3] }, 0.4],
        [{ scaleX: 0.9, scaleY: 0.9, translateZ: 0 }, 0.2],
        [{ scaleX: 1, scaleY: 1 }, 0.5],
      ],
    },
    "transition.bounceOut": {
      defaultDuration: 1e3,
      calls: [
        [{ opacity: [1, 1], scaleX: 0.95, scaleY: 0.95 }, 0.35],
        [{ scaleX: 1.1, scaleY: 1.1, translateZ: 0 }, 0.35],
        [{ opacity: 0, scaleX: 0.3, scaleY: 0.3 }, 0.3],
      ],
      reset: { opacity: [1, 1], scaleX: 1, scaleY: 1 },
    },
    "transition.bounceUpIn": {
      defaultDuration: 1e3,
      calls: [
        [{ opacity: [1, "easeOutQuad", 0], translateY: [-30, 1e3] }, 0.6],
        [{ translateY: 10 }, 0.2],
        [{ translateY: 0 }, 0.2],
      ],
    },
    "transition.bounceUpOut": {
      defaultDuration: 1e3,
      calls: [
        [{ opacity: [1, "easeInQuad", 1], translateY: 20 }, 0.2],
        [{ opacity: 0, translateY: -1e3 }, 0.8],
      ],
      reset: { opacity: [1, 1], translateY: 0 },
    },
    "transition.bounceDownIn": {
      defaultDuration: 1e3,
      calls: [
        [{ opacity: [1, "easeOutQuad", 0], translateY: [30, -1e3] }, 0.6],
        [{ translateY: -10 }, 0.2],
        [{ translateY: 0 }, 0.2],
      ],
    },
    "transition.bounceDownOut": {
      defaultDuration: 1e3,
      calls: [
        [{ opacity: [1, "easeInQuad", 1], translateY: -20 }, 0.2],
        [{ opacity: 0, translateY: 1e3 }, 0.8],
      ],
      reset: { opacity: [1, 1], translateY: 0 },
    },
    "transition.bounceLeftIn": {
      defaultDuration: 900,
      calls: [
        [{ opacity: [1, "easeOutQuad", 0], translateX: [30, -1e3] }, 0.6],
        [{ translateX: -10 }, 0.2],
        [{ translateX: 0 }, 0.2],
      ],
    },
    "transition.bounceLeftOut": {
      defaultDuration: 900,
      calls: [
        [{ opacity: [1, "easeOutQuad", 1], translateX: 20 }, 0.2],
        [{ opacity: 0, translateX: -1e3 }, 0.8],
      ],
      reset: { opacity: [1, 1], translateX: 0 },
    },
    "transition.bounceRightIn": {
      defaultDuration: 950,
      calls: [
        [{ opacity: [1, "easeOutQuad", 0], translateX: [-30, 1e3] }, 0.6],
        [{ translateX: 10 }, 0.2],
        [{ translateX: 0 }, 0.2],
      ],
    },
    "transition.bounceRightOut": {
      defaultDuration: 950,
      calls: [
        [{ opacity: [1, "easeOutQuad", 1], translateX: -20 }, 0.2],
        [{ opacity: 0, translateX: 1e3 }, 0.8],
      ],
      reset: { opacity: [1, 1], translateX: 0 },
    },
    "transition.slideUpIn": {
      defaultDuration: 1e3,
      calls: [[{ opacity: [1, 0], translateY: [0, 20], translateZ: 0 }]],
    },
    "transition.slideUpOut": {
      defaultDuration: 1e3,
      calls: [[{ opacity: [0, 1], translateY: -20, translateZ: 0 }]],
      reset: { opacity: [1, 1], translateY: 0 },
    },
    "transition.slideDownIn": {
      defaultDuration: 1e3,
      calls: [[{ opacity: [1, 0], translateY: [0, -20], translateZ: 0 }]],
    },
    "transition.slideDownOut": {
      defaultDuration: 1e3,
      calls: [[{ opacity: [0, 1], translateY: 20, translateZ: 0 }]],
      reset: { opacity: [1, 1], translateY: 0 },
    },
    "transition.slideLeftIn": {
      defaultDuration: 1e3,
      calls: [[{ opacity: [1, 0], translateX: [0, -20], translateZ: 0 }]],
    },
    "transition.slideLeftOut": {
      defaultDuration: 1050,
      calls: [[{ opacity: [0, 1], translateX: -20, translateZ: 0 }]],
      reset: { opacity: [1, 1], translateX: 0 },
    },
    "transition.slideRightIn": {
      defaultDuration: 1e3,
      calls: [[{ opacity: [1, 0], translateX: [0, 20], translateZ: 0 }]],
    },
    "transition.slideRightOut": {
      defaultDuration: 1050,
      calls: [[{ opacity: [0, 1], translateX: 20, translateZ: 0 }]],
      reset: { opacity: [1, 1], translateX: 0 },
    },
    "transition.slideUpBigIn": {
      defaultDuration: 850,
      calls: [[{ opacity: [1, 0], translateY: [0, 75], translateZ: 0 }]],
    },
    "transition.slideUpBigOut": {
      defaultDuration: 850,
      calls: [[{ opacity: [0, 1], translateY: -75, translateZ: 0 }]],
      reset: { opacity: [1, 1], translateY: 0 },
    },
    "transition.slideDownBigIn": {
      defaultDuration: 850,
      calls: [[{ opacity: [1, 0], translateY: [0, -75], translateZ: 0 }]],
    },
    "transition.slideDownBigOut": {
      defaultDuration: 850,
      calls: [[{ opacity: [0, 1], translateY: 75, translateZ: 0 }]],
      reset: { opacity: [1, 1], translateY: 0 },
    },
    "transition.slideLeftBigIn": {
      defaultDuration: 850,
      calls: [[{ opacity: [1, 0], translateX: [0, -75], translateZ: 0 }]],
    },
    "transition.slideLeftBigOut": {
      defaultDuration: 850,
      calls: [[{ opacity: [0, 1], translateX: -75, translateZ: 0 }]],
      reset: { opacity: [1, 1], translateX: 0 },
    },
    "transition.slideRightBigIn": {
      defaultDuration: 850,
      calls: [[{ opacity: [1, 0], translateX: [0, 75], translateZ: 0 }]],
    },
    "transition.slideRightBigOut": {
      defaultDuration: 850,
      calls: [[{ opacity: [0, 1], translateX: 75, translateZ: 0 }]],
      reset: { opacity: [1, 1], translateX: 0 },
    },
    "transition.perspectiveUpIn": {
      defaultDuration: 900,
      calls: [
        [
          {
            opacity: [1, 0],
            transformPerspective: [800, 800],
            transformOriginX: [0, 0],
            transformOriginY: ["100%", "100%"],
            transformOriginZ: [0, 0],
            rotateX: [0, -180],
          },
        ],
      ],
      reset: {
        transformPerspective: 0,
        transformOriginX: "50%",
        transformOriginY: "50%",
      },
    },
    "transition.perspectiveUpOut": {
      defaultDuration: 950,
      calls: [
        [
          {
            opacity: [0, 1],
            transformPerspective: [800, 800],
            transformOriginX: [0, 0],
            transformOriginY: ["100%", "100%"],
            transformOriginZ: [0, 0],
            rotateX: -180,
          },
        ],
      ],
      reset: {
        opacity: [1, 1],
        transformPerspective: 0,
        transformOriginX: "50%",
        transformOriginY: "50%",
        rotateX: 0,
      },
    },
    "transition.perspectiveDownIn": {
      defaultDuration: 925,
      calls: [
        [
          {
            opacity: [1, 0],
            transformPerspective: [800, 800],
            transformOriginX: [0, 0],
            transformOriginY: [0, 0],
            transformOriginZ: [0, 0],
            rotateX: [0, 180],
          },
        ],
      ],
      reset: {
        transformPerspective: 0,
        transformOriginX: "50%",
        transformOriginY: "50%",
      },
    },
    "transition.perspectiveDownOut": {
      defaultDuration: 950,
      calls: [
        [
          {
            opacity: [0, 1],
            transformPerspective: [800, 800],
            transformOriginX: [0, 0],
            transformOriginY: [0, 0],
            transformOriginZ: [0, 0],
            rotateX: 180,
          },
        ],
      ],
      reset: {
        opacity: [1, 1],
        transformPerspective: 0,
        transformOriginX: "50%",
        transformOriginY: "50%",
        rotateX: 0,
      },
    },
    "transition.perspectiveLeftIn": {
      defaultDuration: 950,
      calls: [
        [
          {
            opacity: [1, 0],
            transformPerspective: [2e3, 2e3],
            transformOriginX: [0, 0],
            transformOriginY: [0, 0],
            transformOriginZ: [0, 0],
            rotateY: [0, -180],
          },
        ],
      ],
      reset: {
        transformPerspective: 0,
        transformOriginX: "50%",
        transformOriginY: "50%",
      },
    },
    "transition.perspectiveLeftOut": {
      defaultDuration: 950,
      calls: [
        [
          {
            opacity: [0, 1],
            transformPerspective: [2e3, 2e3],
            transformOriginX: [0, 0],
            transformOriginY: [0, 0],
            transformOriginZ: [0, 0],
            rotateY: -180,
          },
        ],
      ],
      reset: {
        opacity: [1, 1],
        transformPerspective: 0,
        transformOriginX: "50%",
        transformOriginY: "50%",
        rotateY: 0,
      },
    },
    "transition.perspectiveRightIn": {
      defaultDuration: 950,
      calls: [
        [
          {
            opacity: [1, 0],
            transformPerspective: [2e3, 2e3],
            transformOriginX: ["100%", "100%"],
            transformOriginY: [0, 0],
            transformOriginZ: [0, 0],
            rotateY: [0, 180],
          },
        ],
      ],
      reset: {
        transformPerspective: 0,
        transformOriginX: "50%",
        transformOriginY: "50%",
      },
    },
    "transition.perspectiveRightOut": {
      defaultDuration: 950,
      calls: [
        [
          {
            opacity: [0, 1],
            transformPerspective: [2e3, 2e3],
            transformOriginX: ["100%", "100%"],
            transformOriginY: [0, 0],
            transformOriginZ: [0, 0],
            rotateY: 180,
          },
        ],
      ],
      reset: {
        opacity: [1, 1],
        transformPerspective: 0,
        transformOriginX: "50%",
        transformOriginY: "50%",
        rotateY: 0,
      },
    },
  };
  for (var effectName in effects)
    (function (effectName) {
      var effect = effects[effectName];
      Container.Velocity.Sequences[effectName] = function (element, options) {
        for (var callIndex = 0; callIndex < effect.calls.length; callIndex++) {
          var opts = {};
          opts.duration =
            (options.duration || effect.defaultDuration || 1e3) *
            (effect.calls[callIndex][1] || 1);
          opts.easing = "ease";
          opts.loop = effect.calls[callIndex][2];
          if (callIndex === 0) {
            opts.delay = options.delay;
            opts.begin = options.begin;
            if (options.display) opts.display = options.display;
            else if (/In$/.test(effectName))
              opts.display =
                Container.Velocity.CSS.Values.getDisplayType(element);
          }
          if (callIndex === effect.calls.length - 1) {
            if (effect.reset)
              opts.complete = function () {
                options.complete && options.complete.call();
                Container.Velocity.animate(element, effect.reset, {
                  duration: 0,
                  queue: false,
                });
              };
            else opts.complete = options.complete;
            if (/Out$/.test(effectName)) opts.display = "none";
          }
          Container.Velocity.animate(element, effect.calls[callIndex][0], opts);
        }
      };
    })(effectName);
})();
