// supabase-client.js
// --- CORRECCIÓN ---
// Se debe importar la función `createClient` del objeto global `supabase`
// que la librería de Supabase expone. El error original era que intentabas
// usar la variable `supabase` para definirse a sí misma.

const SUPABASE_URL = 'https://ozmrkhuiakxcgcxiciyy.supabase.co';
const SUPABASE_KEY = 'CH7322a#'; // Por seguridad, esta clave debería ser una clave anónima (anon key) y estar en una variable de entorno.

// Corregimos la creación del cliente
export const supabase = supabase.createClient(SUPABASE_URL, SUPABASE_KEY);