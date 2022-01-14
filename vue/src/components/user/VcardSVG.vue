<template>
  <div class="container preload">
    <div class="creditcard">
      <div class="front">
        <div id="ccsingle"></div>
        <svg
          version="1.1"
          id="cardfront"
          xmlns="http://www.w3.org/2000/svg"
          xmlns:xlink="http://www.w3.org/1999/xlink"
          x="0px"
          y="0px"
          viewBox="0 0 750 471"
          style="enable-background: new 0 0 750 471"
          xml:space="preserve"
        >
          <g id="Front">
            <g id="CardBackground">
              <g id="Page-1_1_">
                <g id="amex_1_">
                  <path
                    id="Rectangle-1_1_"
                    class="lightcolor grey"
                    d="M40,0h670c22.1,0,40,17.9,40,40v391c0,22.1-17.9,40-40,40H40c-22.1,0-40-17.9-40-40V40
                            C0,17.9,17.9,0,40,0z"
                  />
                </g>
              </g>
              <path
                class="darkcolor greydark"
                d="M750,431V193.2c-217.6-57.5-556.4-13.5-750,24.9V431c0,22.1,17.9,40,40,40h670C732.1,471,750,453.1,750,431z"
              />
            </g>
            <text transform="matrix(1 0 0 1 469.7754 50)" class="st7 st5 st8">
              Balance
            </text>
            <text
              transform="matrix(1 0 0 1 469.7754 100)"
              id="svgnumber"
              class="st2 st3 st4"
            >
              {{ Balance }}€
            </text>
            <text
              transform="matrix(1 0 0 1 60.106 295.0121)"
              id="svgnumber"
              class="st2 st3 st4"
            >
              {{ VcardNumber }}
            </text>
            <text
              transform="matrix(1 0 0 1 54.1064 428.1723)"
              id="svgname"
              class="st2 st5 st6"
            >
              {{ VcardName }}
            </text>
            <text
              transform="matrix(1 0 0 1 54.1074 389.8793)"
              class="st7 st5 st8"
            >
              Vcardholder name
            </text>
            <text
              transform="matrix(1 0 0 1 479.7754 388.8793)"
              class="st7 st5 st8"
            >
              Max Balance
            </text>
            <text transform="matrix(1 0 0 1 65.1054 241.5)" class="st7 st5 st8">
              Vcard number
            </text>
            <g>
              <text
                transform="matrix(1 0 0 1  479.7754 433.8095)"
                id="svgexpire"
                class="st2 st5 st9"
              >
                {{ MaxBalance }}€
              </text>
            </g>
            <g id="cchip">
              <g>
                <path
                  class="st2"
                  d="M168.1,143.6H82.9c-10.2,0-18.5-8.3-18.5-18.5V74.9c0-10.2,8.3-18.5,18.5-18.5h85.3
                        c10.2,0,18.5,8.3,18.5,18.5v50.2C186.6,135.3,178.3,143.6,168.1,143.6z"
                />
              </g>
              <g>
                <g>
                  <rect x="82" y="70" class="st12" width="1.5" height="60" />
                </g>
                <g>
                  <rect x="167.4" y="70" class="st12" width="1.5" height="60" />
                </g>
                <g>
                  <path
                    class="st12"
                    d="M125.5,130.8c-10.2,0-18.5-8.3-18.5-18.5c0-4.6,1.7-8.9,4.7-12.3c-3-3.4-4.7-7.7-4.7-12.3
                            c0-10.2,8.3-18.5,18.5-18.5s18.5,8.3,18.5,18.5c0,4.6-1.7,8.9-4.7,12.3c3,3.4,4.7,7.7,4.7,12.3
                            C143.9,122.5,135.7,130.8,125.5,130.8z M125.5,70.8c-9.3,0-16.9,7.6-16.9,16.9c0,4.4,1.7,8.6,4.8,11.8l0.5,0.5l-0.5,0.5
                            c-3.1,3.2-4.8,7.4-4.8,11.8c0,9.3,7.6,16.9,16.9,16.9s16.9-7.6,16.9-16.9c0-4.4-1.7-8.6-4.8-11.8l-0.5-0.5l0.5-0.5
                            c3.1-3.2,4.8-7.4,4.8-11.8C142.4,78.4,134.8,70.8,125.5,70.8z"
                  />
                </g>
                <g>
                  <rect
                    x="82.8"
                    y="82.1"
                    class="st12"
                    width="25.8"
                    height="1.5"
                  />
                </g>
                <g>
                  <rect
                    x="82.8"
                    y="117.9"
                    class="st12"
                    width="26.1"
                    height="1.5"
                  />
                </g>
                <g>
                  <rect
                    x="142.4"
                    y="82.1"
                    class="st12"
                    width="25.8"
                    height="1.5"
                  />
                </g>
                <g>
                  <rect
                    x="142"
                    y="117.9"
                    class="st12"
                    width="26.2"
                    height="1.5"
                  />
                </g>
              </g>
            </g>
          </g>
        </svg>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "VcardSVG",
  props: ["Balance", "VcardNumber", "VcardName", "MaxBalance"],
  data() {
    return {};
  },
};
</script>
<style scoped>
body {
  margin: 0;
  padding: 0;
  background-color: #f9f9f9;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -ms-flex-line-pack: center;
  align-content: center;
  -webkit-box-align: center;
  -ms-flex-align: center;
  align-items: center;
  -webkit-box-pack: center;
  -ms-flex-pack: center;
  justify-content: center;
  min-height: 100vh;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
  font-family: "Raleway";
}

.payment-title {
  width: 100%;
  text-align: center;
}

/* CREDIT CARD IMAGE STYLING */
.preload * {
  -webkit-transition: none !important;
  -moz-transition: none !important;
  -ms-transition: none !important;
  -o-transition: none !important;
}

.container {
  width: 100%;
  max-width: 400px;
  max-height: 251px;
  height: 54vw;
  padding: 20px;
}

#ccsingle {
  position: absolute;
  right: 15px;
  top: 20px;
}

#ccsingle svg {
  width: 100px;
  max-height: 60px;
}

.creditcard svg#cardfront,
.creditcard svg#cardback {
  width: 100%;
  -webkit-box-shadow: 1px 5px 6px 0px black;
  box-shadow: 1px 5px 6px 0px black;
  border-radius: 22px;
}

/* CHANGEABLE CARD ELEMENTS */
.creditcard .lightcolor,
.creditcard .darkcolor {
  -webkit-transition: fill 0.5s;
  transition: fill 0.5s;
}

.creditcard .grey {
  fill: #2574a9;
}

.creditcard .greydark {
  fill: #616161;
}

/* FRONT OF CARD */
#svgname {
  text-transform: uppercase;
}

#cardfront .st2 {
  fill: #ffffff;
}

#cardfront .st3 {
  font-family: "Source Code Pro", monospace;
  font-weight: 600;
}

#cardfront .st4 {
  font-size: 54.7817px;
}

#cardfront .st5 {
  font-family: "Source Code Pro", monospace;
  font-weight: 400;
}

#cardfront .st6 {
  font-size: 33.1112px;
}

#cardfront .st7 {
  opacity: 0.6;
  fill: #ffffff;
}

#cardfront .st8 {
  font-size: 24px;
}

#cardfront .st9 {
  font-size: 36.5498px;
}

#cardfront .st10 {
  font-family: "Source Code Pro", monospace;
  font-weight: 300;
}

#cardfront .st11 {
  font-size: 16.1716px;
}

#cardfront .st12 {
  fill: #4c4c4c;
}
</style>
