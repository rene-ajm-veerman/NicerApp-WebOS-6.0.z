        <div class="flex-title">
            <h1>zoned.at url link shortener</h1>
            <h2>soon with more features!</h2> <h3>(delayed due to massive work on <a href="https://nicer.app" target="naMain">https://nicer.app</a> and <a href="https://said.by" target="saidByMain">https://said.by</a>)</h3>
        </div>
        <br/>
        <div class="payload">
            <p class="content">
                <h2 id="payloadTitle">Shorten the following URL</h2>
                <textarea id="urlToShorten" style="width:100%;min-width:500px;height:7em;"></textarea><br/>
                <div id="timespan">
                    <span class="label">Time before URL is automatically redirected : </span><br/>
                    <span class="label">Minutes : </span><span class="field"><input class="fieldTimespan" type="number" id="timespanMin" min="0" max="59" value="0" onchange="zat.onchangeTimespanMin()"></input></span>
                    <span class="label">Seconds : </span><span class="field"><input class="fieldTimespan" type="number" id="timespanSec" min="10" max="59" value="10"></input></span><br/>
                </div>
                <span class="label" style="color:#BBB"><input type="checkbox" id="manualRedirect" value="manual" onchange="zat.onchangeManualRedirect()"></input><label for="manualRedirect" class="manualRedirect">Visitors to this shortened link must manually click 'Redirect now'</label></span><br/>
                <div class="buttonHolder">
                <button onclick="zat.shorten()">
                    <span>Shorten</span>
                </button>
                </div>
            </p>
            <p class="content">
                <div id="results">
                <h3>Your URL was succesfully shortened to :</h3>
                <input id="shortened" type="text"></input><br/>
                <br/>
                 
                <div class="buttonHolder">
                <button onclick="zat.copyShortenedToClipboard()">
                    <span>Copy to clipboard</span>
                </button>
                </div>
                </div>
            </p>
        </div>
        
        <!--<div class="spacer"></div>-->
        
        
        <div class="divSignup">
        <form id="formSignup" action="/" method="POST" style="">
        <div class="signup conditions">
            <p>
                <span class="label">Conditions and membership offers</span>
                <span class="field">
                <select id="planName" name="planName" form="formSignup" onchange="zat.formSignup_plan_change('#planName');">
                    <optgroup>
                    <option value="free" selected>free</option>
                    <option value="9Euro">Member : 9 Euro per month</option>
                    <option value="19Euro">Basic : 19 Euro per month</option>
                    <option value="49Euro">Advanced : 49 Euro per month</option>
                    <option value="99Euro">Enterprise : 99 Euro per month</option>
                    </optgroup>
                </select>
                </span>
            </p>
        </div>

        <div class="offers">
                <div id="offer_free" class="offer freeOffer">
                    <div class="offerBefore"></div>
                    <div class="offerTitle">
                        <span class="spacer"></span>
                        <span><h3>Free service</h3></span>
                    </div>
                    <div class="conditions">
                        <p class="tooltip siteMainTooltipsterTheme tooltipLinks">
                            <span>Links</span>
                            <span>Unlimited</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipRedirects">
                            <span>Redirects</span>
                            <span>one-hundred-thousand (max, per month)<br/>(optionally, you'll be notified via e-mail<br/> when you risk losing clicks)</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipChange">
                            <span>Change the url redirected to</span>
                            <span>not possible</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipCustom">
                            <span>Custom links / branding</span>
                            <span>not possible</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipDelay">
                            <span>Delay before redirect</span>
                            <span>
                                10 seconds (or more, depending on your settings)<br/>
                                (redirect also happens when the user<br/> clicks on 'Redirect now')
                            </span>
                        </p>
                    </div>
                    <!--
                    <div class="buttonHolder">
                    <button class="pricing" onclick="zat.buy('free');">
                        <span>Price : free</span>
                    </button>
                    </div>
                    -->
                </div>
                
                <div id="offer_9Euro" class="offer">
                    <div class="offerBefore"></div>
                    <div class="offerTitle">
                        <span class="spacer"></span>
                        <span><h3>Member</h3></span>
                    </div>
                    <div class="conditions">
                        <p class="tooltip siteMainTooltipsterTheme tooltipLinks">
                            <span>Links</span>
                            <span>Unlimited</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipRedirects">
                            <span>Redirects</span>
                            <span>two-hundred-thousand (max, per month)<br/>(you'll be notified via e-mail<br/> when you risk losing clicks)</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipChange">
                            <span>Change the url redirected</span>
                            <span>to any url</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipCustom">
                            <span>Custom links / branding</span>
                            <span>10 max</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipDelay">
                            <span>Delay before redirect</span>
                            <span>
                                10 seconds (or more, depending on your settings)<br/>
                                (redirect also happens when the user<br/> clicks on 'Redirect now')
                            </span>
                        </p>
                        <p class="pricing tooltip siteMainTooltipsterTheme tooltipPrice" onclick="zat.buy('9Euro');">
                        <span>Price</span>
                        <span>9 Euro per month<br/>
                        or 89 Euro per year, a savings of over 15%!</span>
                        </p>
                    </div>
                    <div class="buttonHolder">
                    <button class="buttonBuy" onclick="zat.buy('9Euro');">
                        <span>Buy</span>
                    </button>
                    </div>
                </div>
                
                <div id="offer_19Euro" class="offer">
                    <div class="offerBefore"></div>
                    <div class="offerTitle">
                        <span class="spacer"></span>
                        <span><h3>Basic</h3></span>
                    </div>
                    <div class="conditions">
                        <p class="tooltip siteMainTooltipsterTheme tooltipLinks">
                            <span>Links</span>
                            <span>Unlimited</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipRedirects">
                            <span>Redirects</span>
                            <span>Unlimited</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipChange">
                            <span>Change the url redirected</span>
                            <span>to any url</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipCustom">
                            <span>Custom links / branding</span>
                            <span>20 max</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipDelay">
                            <span>Delay before redirect</span>
                            <span>
                                10 seconds (or more, depending on your settings)<br/>
                                (redirect also happens when the user<br/> clicks on 'Redirect now')
                            </span>
                        </p>
                        <p class="pricing tooltip siteMainTooltipsterTheme tooltipPrice" onclick="zat.buy('19Euro');">
                        <span>Price</span>
                        <span>19 Euro per month<br/>
                        or 189 Euro per year, a savings of over 15%!</span>
                        </p>
                    </div>
                    <div class="buttonHolder">
                    <button class="buttonBuy" onclick="zat.buy('19Euro');">
                        <span>Buy</span>
                    </button>
                    </div>
                </div>
                
                <div id="offer_49Euro" class="offer">
                    <div class="offerBefore"></div>
                    <div class="offerTitle">
                        <span class="spacer"></span>
                        <span><h3>Advanced</h3></span>
                    </div>
                    <div class="conditions">
                        <p class="tooltip siteMainTooltipsterTheme tooltipLinks">
                            <span>Links</span>
                            <span>Unlimited</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipRedirects">
                            <span>Redirects</span>
                            <span>Unlimited</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipChange">
                            <span>Change the url redirected</span>
                            <span>to any url</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipCustom">
                            <span>Custom links / branding</span>
                            <span>100 max</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipDelay">
                            <span>Delay before redirect</span>
                            <span>
                                5 seconds (or more, depending on your settings)<br/>
                                (redirect also happens when the user<br/> clicks on 'Redirect now')
                            </span>
                        </p>
                        <p class="pricing tooltip siteMainTooltipsterTheme tooltipPrice" onclick="zat.buy('49Euro');">
                        <span>Price</span>
                        <span>49 Euro per month<br/>
                        or 498 Euro per year, a savings of over 15%!</span>
                        </p>
                    </div>
                    <div class="buttonHolder">
                    <button class="buttonBuy" onclick="zat.buy('49Euro');">
                        <span>Buy</span>
                    </button>
                    </div>
                </div>

                <div id="offer_99Euro" class="offer">
                    <div class="offerBefore"></div>
                    <div class="offerTitle">
                        <span class="spacer"></span>
                        <span><h3>Enterprise</h3></span>
                    </div>
                    <div class="conditions">
                        <p class="tooltip siteMainTooltipsterTheme tooltipLinks">
                            <span>Links</span>
                            <span>Unlimited</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipRedirects">
                            <span>Redirects</span>
                            <span>Unlimited</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipChange">
                            <span>Change the url redirected</span>
                            <span>to any url</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipCustom">
                            <span>Custom links / branding</span>
                            <span>Unlimited</span>
                        </p>
                        <p class="tooltip siteMainTooltipsterTheme tooltipDelay">
                            <span>Delay before redirect</span>
                            <span>
                                5 seconds (or more, depending on your settings)<br/>
                                (redirect also happens when the user<br/> clicks on 'Redirect now')
                            </span>
                        </p>
                        <p class="pricing tooltip siteMainTooltipsterTheme tooltipPrice" onclick="zat.buy('99Euro');">
                        <span>Price</span>
                        <span>99 Euro per month<br/>
                        or 998 Euro per year, a savings of over 15%!</span>
                        </p>
                    </div>
                    <div class="buttonHolder">
                    <button class="buttonBuy" onclick="zat.buy('99Euro');">
                        <span>Buy</span>
                    </button>
                    </div>
                </div>
            </div>
            
            <div id="payment_comingSoon" class="payment">
                Due to legal complications and the fact that i have a reliable somewhat-modest-but-enough income stream already,<br/>
                I will be offering the features of the 'Enterprise' plan for <b>free</b> soon. :)<br/>
                This will take approximately 2 weeks to 1 month to complete, because I do need to plan and code everything as if subscription plans *do still* influence how people can possibly work with this site.<br/>
                Signed : <a href="mailto:rene.veerman.netherlands@gmail.com">Rene A.J.M. Veerman</a>, 09/07jul/2021.
            </div>

            <div id="payment_creditcard" class="payment">
                <p>
                    <span class="label">Creditcard number</span>
                    <span class="field"><input type="text" id="cc_number" name="cc_number"></input></span>
                </p>
                <p>
                    <span class="label">Name on card</span>
                    <span class="field"><input type="text" id="cc_name"></input></span>
                </p>
                <p>
                    <span class="label">Security code</span>
                    <span class="field"><input type="text" id="cc_security_code"></input></span>
                </p>
                <p>
                    <span class="label"></span>
                    <span class="field"><input type="text" id=""></input></span>
                </p>
            </div>
            </form>
            </div>
        </div>
