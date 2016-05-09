<!--
/**
 * Copyright 2015 Compropago.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
/**
 * Compropago php-sdk
 * @author Eduardo Aguilar <eduardo.aguilar@compropago.com>
 */
-->


<style>
    .cpbutton{
        display: inline-block;
        height: 38px;
        padding: 0 30px;
        color: #555 !important;
        text-align: center;
        font-size: 11px;
        font-weight: 600;
        line-height: 38px;
        letter-spacing: .1rem;
        text-transform: uppercase;
        text-decoration: none;
        white-space: nowrap;
        background-color: transparent;
        border-radius: 4px;
        border: 1px solid #bbb;
        cursor: pointer;
        box-sizing: border-box;
        margin-bottom: 1rem;
    }

    .cpbutton:hover,
    .cpbutton:focus{
        color: #333 !important;
        border-color: #888;
        outline: 0;
    }

    .cpbutton.cpbutton-primary{
        color: #FFF !important;
        background-color: #33C3F0;
        border-color: #33C3F0;
    }

    .cpbutton.cpbutton-primary:hover,
    .cpbutton.cpbutton-primary:focus{
        color: #FFF !important;
        background-color: #1EAEDB;
        border-color: #1EAEDB;
    }
</style>

<form action="https://www.compropago.com/comprobante/" method="post">
    <input type="hidden" name="public_key" value=":publickey:" />

    <input type="hidden" name="app_client_name" value="WHMCS" />
    <input type="hidden" name="app_client_version" value="1.1" />

    <input type="hidden" name="customer_data_blocked"   value="false" />
    <input type="hidden" name="customer_name"           value=":customer_name:" />
    <input type="hidden" name="customer_email"          value=":customer_email:" />
    <input type="hidden" name="product_price"           value=":product_price:" />
    <input type="hidden" name="product_id"              value=":product_id:" />
    <input type="hidden" name="product_name"            value=":product_name:" />
    <input type="hidden" name="success_url"             value=":success_url:" />
    <input type="hidden" name="failed_url"              value=":failed_url:" />
    <input type="submit" alt="Compropago" class="cpbutton cpbutton-primary" value="Pagar en Efectivo" />
</form>