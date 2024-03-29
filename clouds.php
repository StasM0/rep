<script src="/cdn-cgi/apps/head/EITrDSV-B1Ztm8rwb4ysI9ep7Dk.js"></script>
<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        z-index: 10;
    }

    #body {
        color: #eee;
        text-shadow: 0 -1px 0 rgba(0, 0, 0, .6);
        font-family: 'Open Sans', sans-serif;
        font-size: 13px;
        line-height: 16px;
        overflow: hidden;
        width: 900px !important;
        height: 900px !important;
    }

    #viewport {
        -webkit-perspective: 1000;
        -moz-perspective: 1000;
        -o-perspective: 1000;
        position: absolute;
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
        overflow: hidden;
        background-image: linear-gradient(bottom, rgb(69, 132, 180) 28%, rgb(31, 71, 120) 64%);
        background-image: -o-linear-gradient(bottom, rgb(69, 132, 180) 28%, rgb(31, 71, 120) 64%);
        background-image: -moz-linear-gradient(bottom, rgb(69, 132, 180) 28%, rgb(31, 71, 120) 64%);
        background-image: -webkit-linear-gradient(bottom, rgb(69, 132, 180) 28%, rgb(31, 71, 120) 64%);
        background-image: -ms-linear-gradient(bottom, rgb(69, 132, 180) 28%, rgb(31, 71, 120) 64%);

        background-image: -webkit-gradient(linear,
                left bottom,
                left top,
                color-stop(0.28, rgb(69, 132, 180)),
                color-stop(0.64, rgb(31, 71, 120)));
    }

    #world {
        position: absolute;
        left: 50%;
        top: 50%;
        margin-left: -256px;
        margin-top: -256px;
        height: 512px;
        width: 512px;
        -webkit-transform-style: preserve-3d;
        -moz-transform-style: preserve-3d;
        -o-transform-style: preserve-3d;
    }

    #world div {
        -webkit-transform-style: preserve-3d;
        -moz-transform-style: preserve-3d;
        -o-transform-style: preserve-3d;
    }

    .cloudBase {
        position: absolute;
        left: 256px;
        top: 256px;
        width: 20px;
        height: 20px;
        margin-left: -10px;
        margin-top: -10px;
    }

    .cloudLayer {
        position: absolute;
        left: 50%;
        top: 50%;
        width: 256px;
        height: 256px;
        margin-left: -128px;
        margin-top: -128px;
        -webkit-transition: opacity .5s ease-out;
        -moz-transition: opacity .5s ease-out;
        -o-transition: opacity .5s ease-out;
    }
</style>


<div id="body">

    <div id="viewport">
        <div id="world">
        </div>
    </div>

    <script>
        (function() {
            var lastTime = 0;
            var vendors = ['ms', 'moz', 'webkit', 'o'];
            for (var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
                window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
                window.cancelRequestAnimationFrame = window[vendors[x] +
                    'CancelRequestAnimationFrame'];
            }

            if (!window.requestAnimationFrame)
                window.requestAnimationFrame = function(callback, element) {
                    var currTime = new Date().getTime();
                    var timeToCall = Math.max(0, 16 - (currTime - lastTime));
                    var id = window.setTimeout(function() {
                            callback(currTime + timeToCall);
                        },
                        timeToCall);
                    lastTime = currTime + timeToCall;
                    return id;
                };

            if (!window.cancelAnimationFrame)
                window.cancelAnimationFrame = function(id) {
                    clearTimeout(id);
                };
        }())

        var layers = [],
            objects = [],

            world = document.getElementById('world'),
            viewport = document.getElementById('viewport'),

            d = 0,
            p = 400,
            worldXAngle = 0,
            worldYAngle = 0;

        viewport.style.webkitPerspective = p;
        viewport.style.MozPerspective = p;
        viewport.style.oPerspective = p;

        generate();

        function createCloud() {

            var div = document.createElement('div');
            div.className = 'cloudBase';
            var x = 256 - (Math.random() * 512);
            var y = 256 - (Math.random() * 512);
            var z = 256 - (Math.random() * 512);
            var t = 'translateX( ' + x + 'px ) translateY( ' + y + 'px ) translateZ( ' + z + 'px )';
            div.style.webkitTransform = t;
            div.style.MozTransform = t;
            div.style.oTransform = t;
            world.appendChild(div);

            for (var j = 0; j < 5 + Math.round(Math.random() * 10); j++) {
                var cloud = document.createElement('img');
                cloud.style.opacity = 0;
                var r = Math.random();
                var src = 'cloud.png';
                (function(img) {
                    img.addEventListener('load', function() {
                        img.style.opacity = .8;
                    })
                })(cloud);
                cloud.setAttribute('src', src);
                cloud.className = 'cloudLayer';

                var x = 256 - (Math.random() * 512);
                var y = 256 - (Math.random() * 512);
                var z = 100 - (Math.random() * 200);
                var a = Math.random() * 360;
                var s = .25 + Math.random();
                x *= .2;
                y *= .2;
                cloud.data = {
                    x: x,
                    y: y,
                    z: z,
                    a: a,
                    s: s,
                    speed: .1 * Math.random()
                };
                var t = 'translateX( ' + x + 'px ) translateY( ' + y + 'px ) translateZ( ' + z + 'px ) rotateZ( ' + a + 'deg ) scale( ' + s + ' )';
                cloud.style.webkitTransform = t;
                cloud.style.MozTransform = t;
                cloud.style.oTransform = t;

                div.appendChild(cloud);
                layers.push(cloud);
            }

            return div;
        }

        window.addEventListener('mousewheel', onContainerMouseWheel);
        window.addEventListener('DOMMouseScroll', onContainerMouseWheel);
        window.addEventListener('mousemove', onMouseMove);
        window.addEventListener('touchmove', onMouseMove);

        function onMouseMove(e) {

            var x = e.clientX || e.touches[0].clientX;
            var y = e.clientY || e.touches[0].clientY;

            worldYAngle = -(.5 - (x / window.innerWidth)) * 180;
            worldXAngle = (.5 - (y / window.innerHeight)) * 180;
            updateView();
            event.preventDefault();

        }

        function onContainerMouseWheel(event) {

            event = event ? event : window.event;
            d = d - (event.detail ? event.detail * -5 : event.wheelDelta / 8);
            updateView();
            event.preventDefault();

        }

        function generate() {

            objects = [];

            if (world.hasChildNodes()) {
                while (world.childNodes.length >= 1) {
                    world.removeChild(world.firstChild);
                }
            }

            for (var j = 0; j < 5; j++) {
                objects.push(createCloud());
            }

        }

        function updateView() {
            var t = 'translateZ( ' + d + 'px ) rotateX( ' + worldXAngle + 'deg) rotateY( ' + worldYAngle + 'deg)';
            world.style.webkitTransform = t;
            world.style.MozTransform = t;
            world.style.oTransform = t;
        }

        function update() {

            for (var j = 0; j < layers.length; j++) {
                var layer = layers[j];
                layer.data.a += layer.data.speed;
                var t = 'translateX( ' + layer.data.x + 'px ) translateY( ' + layer.data.y + 'px ) translateZ( ' + layer.data.z + 'px ) rotateY( ' + (-worldYAngle) + 'deg ) rotateX( ' + (-worldXAngle) + 'deg ) rotateZ( ' + layer.data.a + 'deg ) scale( ' + layer.data.s + ')';
                layer.style.webkitTransform = t;
                layer.style.MozTransform = t;
                layer.style.oTransform = t;
            }

            requestAnimationFrame(update);

        }

        update();
    </script>

</div>