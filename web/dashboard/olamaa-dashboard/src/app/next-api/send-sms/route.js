import { NextResponse } from "next/server";
import axios from "axios";
import https from "https";

function stringToUnicodeHex(text = "") {
  return Array.from(text)
    .map((char) => char.charCodeAt(0).toString(16).padStart(4, "0").toUpperCase())
    .join("");
}

function normalizeSyrianPhone(phone = "") {
  const raw = String(phone).trim().replace(/[^\d+]/g, "");
  if (!raw) return "";
  if (raw.startsWith("+963")) return raw.slice(1);
  if (raw.startsWith("963")) return raw;
  if (raw.startsWith("09")) return `963${raw.slice(1)}`;
  if (raw.startsWith("9")) return `963${raw}`;
  return raw;
}

function normalizeLang(lang) {
  return String(lang) === "1" ? 1 : 0;
}

function getParams({ phone, msgHex, lang }) {
  return {
    User: "olmlmrr802",
    Pass: "olaasd181012",
    From: "Al Olamaa",
    Gsm: phone,
    Msg: msgHex,
    Lang: lang,
  };
}

/**
 * 🚀 Simple retry wrapper for axios
 */
async function axiosWithRetry(config, retries = 2) {
  for (let i = 0; i <= retries; i++) {
    try {
      return await axios(config);
    } catch (err) {
      const isLast = i === retries;
      const isTimeout = err.code === "ECONNABORTED" || err.message.includes("timeout") || err.message.includes("Timeout");
      
      console.error(`[SMS API] Attempt ${i + 1} failed: ${err.message}`);
      
      if (isLast || !isTimeout) throw err;
      
      // Wait a bit before retry
      await new Promise(r => setTimeout(r, 1000 * (i + 1)));
    }
  }
}

export async function POST(request) {
  try {
    const body = await request.json();

    const phone = normalizeSyrianPhone(body?.phone || "");
    const message = String(body?.message || "");
    const lang = normalizeLang(body?.lang ?? 0);

    if (!phone) {
      return NextResponse.json(
        { status: false, message: "رقم الهاتف مطلوب" },
        { status: 400 },
      );
    }

    if (!message.trim()) {
      return NextResponse.json(
        { status: false, message: "نص الرسالة مطلوب" },
        { status: 400 },
      );
    }

    const msgHex = lang === 1 ? stringToUnicodeHex(message) : message;
    const params = getParams({ phone, msgHex, lang });
    const baseUrl = "https://services.mtnsyr.com:7443/general/MTNSERVICES/ConcatenatedSender.aspx";

    console.log(`[SMS API] Sending to ${phone} with Lang: ${lang}`);

    let response;
    try {
      response = await axiosWithRetry({
        url: baseUrl,
        method: "GET",
        params, // Axios will handle encoding (mostly %20 for spaces)
        timeout: 30000, // 30 seconds
        validateStatus: () => true,
        httpsAgent: new https.Agent({
          rejectUnauthorized: false,
        }),
      });
    } catch (error) {
      console.error("[SMS API] Error after retries:", error.message);
      return NextResponse.json(
        {
          status: false,
          message: "فشل الاتصال بمزود الرسائل بعد عدة محاولات",
          upstream_error: error?.message || "connection failed",
          provider_host: "services.mtnsyr.com:7443",
        },
        { status: 502 },
      );
    }

    const providerResponse = String(response.data || "");
    console.log(`[SMS API] Provider Response: ${providerResponse}`);

    if (response.status < 200 || response.status >= 300) {
      return NextResponse.json(
        {
          status: false,
          message: "فشل في إرسال الرسالة عبر المزود",
          provider_status: response.status,
          provider_response: providerResponse,
        },
        { status: 502 },
      );
    }

    return NextResponse.json({
      status: true,
      message: "تم إرسال الرسالة بنجاح",
      provider_response: providerResponse,
    });
  } catch (error) {
    console.error("[SMS API] Internal error:", error);
    return NextResponse.json(
      {
        status: false,
        message: "حدث خطأ أثناء معالجة طلب إرسال الرسالة",
        error: error?.message || "Unknown error",
      },
      { status: 500 },
    );
  }
}

