import React, { useState } from 'react';
import { 
  FileText, 
  Download, 
  Printer, 
  DollarSign, 
  Boxes, 
  AlertCircle, 
  CheckCircle2, 
  Calendar,
  Layers,
  Eye,
  X,
  Truck,
  Scale,
  CheckBadge,
  UserCheck,
  Wrench,
  Clock
} from 'lucide-react';
import { Proyecto, Incidencia, ItemInventario, Almacen } from '../types';

interface ReportesViewProps {
  proyectos: Proyecto[];
  incidencias: Incidencia[];
  items: ItemInventario[];
  almacenes: Almacen[];
}

export const ReportesView: React.FC<ReportesViewProps> = ({
  proyectos,
  incidencias,
  items,
  almacenes,
}) => {
  const [periodo, setPeriodo] = useState<'diario' | 'semanal' | 'mensual' | 'anual'>('mensual');
  const [selectedTecnico, setSelectedTecnico] = useState<string>('todos');
  const [selectedCliente, setSelectedCliente] = useState<string>('todos');
  const [showPreviewModal, setShowPreviewModal] = useState<boolean>(false);

  // Totales
  const totalPresupuesto = proyectos.reduce((acc, p) => acc + (p.presupuesto_total || 0), 0);
  const totalItemsValuation = items.reduce((acc, i) => acc + ((i.stock_actual || 0) * (i.precio_unitario || 0)), 0);
  const incidenciasResueltas = incidencias.filter(i => i.estado === 'Resuelta' || i.estado === 'Cerrada').length;
  const tasaResolucion = incidencias.length > 0 ? Math.round((incidenciasResueltas / incidencias.length) * 100) : 100;

  // Gastos de campo (transporte y almuerzo)
  const totalTransporte = incidencias.reduce((acc, i) => acc + (i.gasto_transporte || 0), 0);
  const totalAlmuerzo = incidencias.reduce((acc, i) => acc + (i.gasto_almuerzo || 0), 0);
  const totalGastosCampo = totalTransporte + totalAlmuerzo;

  // Balance neto operativo
  const balanceNeto = totalPresupuesto - totalGastosCampo;

  // SLAs
  const incidenciasConSla = incidencias.filter(i => i.cumplio_sla !== undefined);
  const dentroSla = incidenciasConSla.filter(i => i.cumplio_sla === true).length;
  const fueraSla = incidenciasConSla.filter(i => i.cumplio_sla === false).length;
  const tasaSla = incidenciasConSla.length > 0 ? Math.round((dentroSla / incidenciasConSla.length) * 100) : 100;

  // Especialistas únicos
  const tecnicosSet = new Set<string>();
  incidencias.forEach(i => {
    if (i.tecnico_nombre) tecnicosSet.add(i.tecnico_nombre);
  });
  const tecnicos = Array.from(tecnicosSet);

  // Agrupación de gastos por especialista
  const gastosPorEspecialista = tecnicos.map(nombre => {
    const list = incidencias.filter(i => i.tecnico_nombre === nombre);
    const transp = list.reduce((acc, i) => acc + (i.gasto_transporte || 0), 0);
    const almu = list.reduce((acc, i) => acc + (i.gasto_almuerzo || 0), 0);
    return {
      nombre,
      total_incidencias: list.length,
      transporte: transp,
      almuerzo: almu,
      total: transp + almu
    };
  });

  // Clientes únicos
  const clientesSet = new Set<string>();
  incidencias.forEach(i => {
    if (i.cliente_nombre) clientesSet.add(i.cliente_nombre);
  });
  const clientes = Array.from(clientesSet);

  // Filtrado de incidencias
  const incidenciasFiltradas = incidencias.filter(i => {
    if (selectedTecnico !== 'todos' && i.tecnico_nombre !== selectedTecnico) return false;
    if (selectedCliente !== 'todos' && i.cliente_nombre !== selectedCliente) return false;
    return true;
  });

  // Balance mes a mes simulado para el año 2026
  const meses = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
  ];
  const balanceMeses = meses.map((mes, idx) => {
    const proy = idx === 9 ? totalPresupuesto : 0;
    const serv = 0;
    const ing = proy + serv;
    const trans = idx === 9 ? totalTransporte : 0;
    const alm = idx === 9 ? totalAlmuerzo : 0;
    const gCampo = trans + alm;
    return {
      mes,
      proyectos: proy,
      servicios: serv,
      ingresos: ing,
      transporte: trans,
      almuerzo: alm,
      gastos_campo: gCampo,
      margen_neto: ing - gCampo
    };
  });

  // Exportar a CSV
  const handleExportCSV = () => {
    const rows = [
      ['Modulo', 'Codigo', 'Nombre/Titulo', 'Estado', 'Monto/Valor'],
      ...proyectos.map(p => ['Proyecto', p.codigo, p.nombre, p.estado, p.presupuesto_total.toString()]),
      ...incidencias.map(i => ['Incidencia', i.codigo, i.titulo, i.estado, (i.costo_estimado || 0).toString()]),
      ...items.map(it => ['Inventario', it.codigo, it.nombre, `Stock: ${it.stock_actual}`, ((it.stock_actual || 0) * (it.precio_unitario || 0)).toString()])
    ];
    const csvContent = 'data:text/csv;charset=utf-8,' + rows.map(e => e.join(',')).join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `dataplus_reporte_${new Date().toISOString().slice(0, 10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  // Motor de Impresión Aislado (Garantiza que la vista previa NUNCA salga en blanco)
  const handlePrint = () => {
    const sourceEl = document.getElementById('dp-react-printable-sheet');
    if (!sourceEl) {
      window.print();
      return;
    }

    const existingFrame = document.getElementById('dp_react_print_frame');
    if (existingFrame) {
      existingFrame.remove();
    }

    const iframe = document.createElement('iframe');
    iframe.id = 'dp_react_print_frame';
    iframe.setAttribute('style', 'position:fixed;top:-9999px;left:-9999px;width:1024px;height:768px;border:none;visibility:hidden;');
    document.body.appendChild(iframe);

    const doc = iframe.contentWindow?.document;
    if (!doc) {
      window.print();
      return;
    }

    doc.open();
    doc.write(`<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte Oficial - DataPlus S.R.L.</title>
  <style>
    @page { size: letter portrait; margin: 12mm 15mm 15mm 15mm; }
    *, *::before, *::after { box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    body { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #ffffff !important; color: #0f172a !important; font-size: 11px; line-height: 1.35; }
    h1, h2, h3, h4, p { margin: 0; }
    table { width: 100% !important; border-collapse: collapse !important; }
    .sheet-box { width: 100% !important; max-width: 100% !important; padding: 0 !important; margin: 0 !important; box-shadow: none !important; border: none !important; }
    .print-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 16px; }
    .hero-banner { background: #f8fafc; border: 1.5px solid #0f172a; border-radius: 8px; padding: 14px 18px; margin-bottom: 16px; }
    .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 16px; }
    .card { background: #ffffff; border: 1px solid #94a3b8; border-radius: 6px; padding: 8px 10px; }
    .panel { background: #ffffff; border: 1px solid #94a3b8; border-radius: 6px; padding: 10px; margin-bottom: 14px; page-break-inside: avoid; }
    .panel-hdr { border-bottom: 1px solid #cbd5e1; padding-bottom: 6px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; }
    th { background: #f1f5f9; color: #0f172a; font-weight: 700; border-bottom: 1.5px solid #0f172a; padding: 5px 6px; text-align: left; font-size: 8.5px; text-transform: uppercase; }
    td { color: #0f172a; border-bottom: 1px solid #e2e8f0; padding: 5px 6px; font-size: 8.5px; vertical-align: top; }
    .signatures { display: grid; grid-template-columns: repeat(2, 1fr); gap: 50px; margin-top: 32px; padding-top: 8px; page-break-inside: avoid; }
    .sig-line { border-top: 1.5px solid #0f172a; text-align: center; padding-top: 5px; font-size: 9.5px; font-weight: 700; color: #0f172a; }
  </style>
</head>
<body>
  <div class="sheet-box">
    ${sourceEl.innerHTML}
  </div>
</body>
</html>`);
    doc.close();

    setTimeout(() => {
      try {
        iframe.contentWindow?.focus();
        iframe.contentWindow?.print();
      } catch (err) {
        console.error('Fallback window.print():', err);
        window.print();
      }
    }, 250);
  };

  return (
    <div className="space-y-6">
      {/* Encabezado Principal y Botones de Acción */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <FileText className="h-5 w-5 text-teal-400" />
            Centro de Reportes & Balances Operativos
          </h2>
          <p className="text-xs text-slate-400">
            Rendición ejecutiva de fondos, viáticos en terreno (transporte y almuerzo) y estados de servicio.
          </p>
        </div>
        <div className="flex items-center gap-2">
          <button
            onClick={() => setShowPreviewModal(true)}
            className="flex items-center gap-1.5 rounded-lg border border-sky-500/40 bg-slate-800/80 hover:bg-slate-700 text-sky-300 font-semibold px-3 py-2 text-xs transition-colors cursor-pointer"
            title="Ver vista previa oficial en hoja de papel antes de imprimir"
          >
            <Eye className="h-3.5 w-3.5 text-sky-400" />
            Vista Previa
          </button>
          <button
            onClick={handlePrint}
            className="flex items-center gap-1.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white font-bold px-3 py-2 text-xs transition-all shadow-md shadow-sky-600/20 cursor-pointer"
            title="Imprimir documento oficial directamente"
          >
            <Printer className="h-3.5 w-3.5" />
            Imprimir / PDF
          </button>
          <button
            onClick={handleExportCSV}
            className="flex items-center gap-1.5 rounded-lg bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold px-4 py-2 text-xs transition-all shadow-md shadow-teal-500/20 cursor-pointer"
          >
            <Download className="h-4 w-4" />
            Exportar CSV
          </button>
        </div>
      </div>

      {/* Barra de Filtros Interactivos */}
      <div className="rounded-2xl border border-slate-800 bg-slate-900/90 p-4 space-y-4">
        <div className="flex items-center gap-2 border-b border-slate-800 pb-3 overflow-x-auto">
          <button
            onClick={() => setPeriodo('diario')}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 ${
              periodo === 'diario' ? 'bg-sky-500 text-white shadow' : 'text-slate-400 hover:text-white hover:bg-slate-800'
            }`}
          >
            <Clock className="h-3.5 w-3.5" />
            Diario
          </button>
          <button
            onClick={() => setPeriodo('semanal')}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 ${
              periodo === 'semanal' ? 'bg-sky-500 text-white shadow' : 'text-slate-400 hover:text-white hover:bg-slate-800'
            }`}
          >
            <Calendar className="h-3.5 w-3.5" />
            Semanal
          </button>
          <button
            onClick={() => setPeriodo('mensual')}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 ${
              periodo === 'mensual' ? 'bg-sky-500 text-white shadow' : 'text-slate-400 hover:text-white hover:bg-slate-800'
            }`}
          >
            <Calendar className="h-3.5 w-3.5" />
            Mensual (Octubre 2026)
          </button>
          <button
            onClick={() => setPeriodo('anual')}
            className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 ${
              periodo === 'anual' ? 'bg-sky-500 text-white shadow' : 'text-slate-400 hover:text-white hover:bg-slate-800'
            }`}
          >
            <Scale className="h-3.5 w-3.5" />
            Balance Anual 2026
          </button>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label className="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">
              Especialista / Técnico
            </label>
            <select
              value={selectedTecnico}
              onChange={(e) => setSelectedTecnico(e.target.value)}
              className="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-xs text-white outline-none focus:border-sky-500 cursor-pointer"
            >
              <option value="todos">Todos los especialistas</option>
              {tecnicos.map(t => (
                <option key={t} value={t}>{t}</option>
              ))}
            </select>
          </div>
          <div>
            <label className="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">
              Cliente
            </label>
            <select
              value={selectedCliente}
              onChange={(e) => setSelectedCliente(e.target.value)}
              className="w-full rounded-lg border border-slate-700 bg-slate-950 px-3 py-2 text-xs text-white outline-none focus:border-sky-500 cursor-pointer"
            >
              <option value="todos">Todos los clientes</option>
              {clientes.map(c => (
                <option key={c} value={c}>{c}</option>
              ))}
            </select>
          </div>
        </div>
      </div>

      {/* Grid de 4 KPIs Principales */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
          <div className="flex items-center justify-between text-slate-400">
            <span className="text-xs font-semibold uppercase tracking-wider">Dinero Generado</span>
            <div className="rounded-lg bg-sky-500/10 p-2 text-sky-400 border border-sky-500/20">
              <DollarSign className="h-4 w-4" />
            </div>
          </div>
          <div className="mt-3 text-2xl font-bold text-sky-400 font-mono">
            ${totalPresupuesto.toLocaleString('en-US', { minimumFractionDigits: 2 })}
          </div>
          <div className="mt-1 text-xs text-slate-400">
            Obras, proyectos y contratos
          </div>
        </div>

        <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
          <div className="flex items-center justify-between text-slate-400">
            <span className="text-xs font-semibold uppercase tracking-wider">Gastos Especialistas</span>
            <div className="rounded-lg bg-amber-500/10 p-2 text-amber-400 border border-amber-500/20">
              <Truck className="h-4 w-4" />
            </div>
          </div>
          <div className="mt-3 text-2xl font-bold text-amber-400 font-mono">
            ${totalGastosCampo.toLocaleString('en-US', { minimumFractionDigits: 2 })}
          </div>
          <div className="mt-1 text-xs text-slate-400">
            Transporte: ${totalTransporte.toLocaleString()} · Almuerzo: ${totalAlmuerzo.toLocaleString()}
          </div>
        </div>

        <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
          <div className="flex items-center justify-between text-slate-400">
            <span className="text-xs font-semibold uppercase tracking-wider">Balance Neto</span>
            <div className="rounded-lg bg-emerald-500/10 p-2 text-emerald-400 border border-emerald-500/20">
              <Scale className="h-4 w-4" />
            </div>
          </div>
          <div className="mt-3 text-2xl font-bold text-emerald-400 font-mono">
            ${balanceNeto.toLocaleString('en-US', { minimumFractionDigits: 2 })}
          </div>
          <div className="mt-1 text-xs text-slate-400">
            Margen operativo de servicios
          </div>
        </div>

        <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
          <div className="flex items-center justify-between text-slate-400">
            <span className="text-xs font-semibold uppercase tracking-wider">Cumplimiento SLA</span>
            <div className="rounded-lg bg-purple-500/10 p-2 text-purple-400 border border-purple-500/20">
              <CheckCircle2 className="h-4 w-4" />
            </div>
          </div>
          <div className="mt-3 text-2xl font-bold text-purple-400 font-mono">
            {tasaSla}%
          </div>
          <div className="mt-1 text-xs text-slate-400">
            {dentroSla} dentro / {fueraSla} fuera de SLA
          </div>
        </div>
      </div>

      {/* Tabla 1: Rendición de Gastos de Especialistas */}
      <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
        <h3 className="text-sm font-bold text-white mb-3 flex items-center justify-between">
          <span className="flex items-center gap-2">
            <Truck className="h-4 w-4 text-amber-400" />
            Rendición de Gastos de Especialistas (Transporte & Almuerzo)
          </span>
          <span className="text-xs font-mono font-bold text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-md border border-amber-500/20">
            Total en Terreno: ${totalGastosCampo.toLocaleString('en-US', { minimumFractionDigits: 2 })} CUP
          </span>
        </h3>
        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead className="border-b border-slate-800 bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
              <tr>
                <th className="px-4 py-3">Especialista / Técnico</th>
                <th className="px-4 py-3 text-center">Asistencias Atendidas</th>
                <th className="px-4 py-3 text-right">Gasto Transporte</th>
                <th className="px-4 py-3 text-right">Gasto Almuerzo</th>
                <th className="px-4 py-3 text-right">Total Gastos en Terreno</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800 text-slate-200">
              {gastosPorEspecialista.map((esp, i) => (
                <tr key={i} className="hover:bg-slate-800/40">
                  <td className="px-4 py-3 font-semibold text-white">{esp.nombre}</td>
                  <td className="px-4 py-3 text-center font-bold">{esp.total_incidencias}</td>
                  <td className="px-4 py-3 text-right font-mono text-sky-400">
                    ${esp.transporte.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                  </td>
                  <td className="px-4 py-3 text-right font-mono text-amber-400">
                    ${esp.almuerzo.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                  </td>
                  <td className="px-4 py-3 text-right font-mono font-bold text-white">
                    ${esp.total.toLocaleString('en-US', { minimumFractionDigits: 2 })} CUP
                  </td>
                </tr>
              ))}
              {gastosPorEspecialista.length === 0 && (
                <tr>
                  <td colSpan={5} className="px-4 py-8 text-center text-slate-500">
                    No hay gastos de campo reportados por especialistas en este período.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Tabla 2: Balance Anual (solo si período es Anual) */}
      {periodo === 'anual' && (
        <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
          <h3 className="text-sm font-bold text-white mb-3 flex items-center justify-between">
            <span className="flex items-center gap-2">
              <Scale className="h-4 w-4 text-emerald-400" />
              Balance Financiero Mes a Mes — Año Fiscal 2026
            </span>
            <span className="text-xs font-mono font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-md border border-emerald-500/20">
              12 Meses Consolidados
            </span>
          </h3>
          <div className="overflow-x-auto">
            <table className="w-full text-left text-xs">
              <thead className="border-b border-slate-800 bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                <tr>
                  <th className="px-4 py-3">Mes</th>
                  <th className="px-4 py-3 text-right">Obras & Proyectos</th>
                  <th className="px-4 py-3 text-right">Total Ingresos</th>
                  <th className="px-4 py-3 text-right">Transporte</th>
                  <th className="px-4 py-3 text-right">Almuerzo</th>
                  <th className="px-4 py-3 text-right">Total Gastos Campo</th>
                  <th className="px-4 py-3 text-right">Balance Neto Operativo</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800 text-slate-200">
                {balanceMeses.map((bm, i) => (
                  <tr key={i} className="hover:bg-slate-800/40">
                    <td className="px-4 py-3 font-semibold text-white">{bm.mes}</td>
                    <td className="px-4 py-3 text-right font-mono text-sky-400">${bm.proyectos.toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                    <td className="px-4 py-3 text-right font-mono font-bold text-white">${bm.ingresos.toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                    <td className="px-4 py-3 text-right font-mono text-slate-400">${bm.transporte.toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                    <td className="px-4 py-3 text-right font-mono text-slate-400">${bm.almuerzo.toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                    <td className="px-4 py-3 text-right font-mono text-amber-400">${bm.gastos_campo.toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                    <td className={`px-4 py-3 text-right font-mono font-bold ${bm.margen_neto >= 0 ? 'text-emerald-400' : 'text-rose-400'}`}>
                      ${bm.margen_neto.toLocaleString('en-US', { minimumFractionDigits: 2 })} CUP
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Tabla 3: Incidencias y Asistencias Técnicas */}
      <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
        <h3 className="text-sm font-bold text-white mb-3 flex items-center justify-between">
          <span className="flex items-center gap-2">
            <Wrench className="h-4 w-4 text-sky-400" />
            Registro de Incidencias Técnicas & Cumplimiento de SLA
          </span>
          <span className="text-xs text-slate-400">
            {incidenciasFiltradas.length} asistencias
          </span>
        </h3>
        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead className="border-b border-slate-800 bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
              <tr>
                <th className="px-4 py-3">Código / Fecha</th>
                <th className="px-4 py-3">Cliente</th>
                <th className="px-4 py-3">Servicio</th>
                <th className="px-4 py-3">Especialista</th>
                <th className="px-4 py-3">Estado / SLA</th>
                <th className="px-4 py-3 text-right">Gasto Transp.</th>
                <th className="px-4 py-3 text-right">Gasto Alm.</th>
                <th className="px-4 py-3 text-right">Total Gastos</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800 text-slate-200">
              {incidenciasFiltradas.map((inc) => (
                <tr key={inc.id} className="hover:bg-slate-800/40">
                  <td className="px-4 py-3">
                    <span className="font-mono font-bold text-sky-400">{inc.codigo}</span>
                    <div className="text-[10px] text-slate-400">{inc.fecha_reporte}</div>
                  </td>
                  <td className="px-4 py-3">
                    <div className="font-semibold text-white">{inc.cliente_nombre}</div>
                    <div className="text-[10px] text-slate-400">{inc.titulo}</div>
                  </td>
                  <td className="px-4 py-3">
                    <span className="rounded bg-sky-500/10 px-2 py-0.5 text-[10px] font-bold text-sky-400 border border-sky-500/20">
                      {inc.tipo}
                    </span>
                  </td>
                  <td className="px-4 py-3 text-slate-300">
                    {inc.tecnico_nombre || 'Sin asignar'}
                  </td>
                  <td className="px-4 py-3">
                    {inc.cumplio_sla === true ? (
                      <span className="text-emerald-400 font-bold">✓ En SLA</span>
                    ) : inc.cumplio_sla === false ? (
                      <span className="text-rose-400 font-bold">⚠ Fuera SLA</span>
                    ) : (
                      <span className="text-amber-400 font-bold">{inc.estado}</span>
                    )}
                  </td>
                  <td className="px-4 py-3 text-right font-mono">
                    ${(inc.gasto_transporte || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                  </td>
                  <td className="px-4 py-3 text-right font-mono">
                    ${(inc.gasto_almuerzo || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                  </td>
                  <td className="px-4 py-3 text-right font-mono font-bold text-amber-400">
                    ${((inc.gasto_transporte || 0) + (inc.gasto_almuerzo || 0)).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                  </td>
                </tr>
              ))}
              {incidenciasFiltradas.length === 0 && (
                <tr>
                  <td colSpan={8} className="px-4 py-8 text-center text-slate-500">
                    No hay incidencias registradas en este período.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* ============================================================
          MODAL INTERACTIVO DE VISTA PREVIA DEL DOCUMENTO (A4 / CARTA)
          ============================================================ */}
      {showPreviewModal && (
        <div 
          className="fixed inset-0 z-50 flex flex-col bg-slate-950/90 backdrop-blur-md overflow-y-auto"
          onClick={() => setShowPreviewModal(false)}
        >
          {/* Barra Superior Flotante del Modal */}
          <div 
            className="sticky top-0 z-20 flex items-center justify-between border-b border-slate-800 bg-slate-900 px-6 py-3 shadow-xl"
            onClick={(e) => e.stopPropagation()}
          >
            <div className="flex items-center gap-3">
              <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-500/15 border border-sky-500/30 text-sky-400">
                <FileText className="h-5 w-5" />
              </div>
              <div>
                <h4 className="text-sm font-bold text-white">Vista Previa de Impresión Oficial</h4>
                <p className="text-[11px] text-slate-400">Simulación del documento A4 / Carta con membrete institucional DataPlus</p>
              </div>
            </div>
            <div className="flex items-center gap-2">
              <button
                onClick={handlePrint}
                className="flex items-center gap-1.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white font-bold px-4 py-2 text-xs transition-all shadow-md shadow-sky-600/30 cursor-pointer"
              >
                <Printer className="h-4 w-4" />
                Imprimir / Guardar en PDF
              </button>
              <button
                onClick={() => setShowPreviewModal(false)}
                className="flex items-center gap-1.5 rounded-lg border border-slate-700 bg-slate-800 text-slate-300 hover:bg-slate-700 font-semibold px-3 py-2 text-xs transition-colors cursor-pointer"
              >
                <X className="h-4 w-4" />
                Cerrar
              </button>
            </div>
          </div>

          {/* Hoja de Papel Blanco Simulada */}
          <div 
            className="flex-1 p-6 md:p-10 flex justify-center bg-slate-950/70"
            onClick={(e) => e.stopPropagation()}
          >
            <div 
              id="dp-react-printable-sheet"
              className="w-full max-w-4xl bg-white text-slate-900 rounded-sm shadow-2xl p-8 md:p-12 border border-slate-300 font-sans text-xs leading-relaxed"
            >
              {/* Membrete Oficial */}
              <div className="print-header flex justify-between items-center border-b-2 border-slate-900 pb-3 mb-6">
                <div>
                  <h1 className="text-xl font-black text-slate-900 tracking-tight">DATAPLUS S.R.L.</h1>
                  <p className="text-xs text-slate-600 mt-0.5">Plataforma Oficial de Gestión Técnica, Operativa y Financiera</p>
                </div>
                <div className="text-right text-xs text-slate-600">
                  <p><strong>Fecha de Emisión:</strong> {new Date().toLocaleDateString('es-ES')} {new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}</p>
                  <p className="mt-0.5"><strong>Emitido por:</strong> Administración Central</p>
                </div>
              </div>

              {/* Banner de Período */}
              <div className="hero-banner bg-slate-50 border border-slate-900 rounded-lg p-4 mb-6">
                <div className="flex justify-between items-center gap-4 flex-wrap">
                  <div>
                    <p className="text-[10px] font-bold uppercase text-slate-600 tracking-wider">Documento de Rendición & Control Operativo</p>
                    <h2 className="text-lg font-black text-slate-900 mt-1 capitalize">Reporte {periodo} - DataPlus S.R.L.</h2>
                    <p className="text-xs text-slate-700 mt-1">
                      {incidencias.length} asistencias técnicas · {proyectos.length} proyectos vinculados · Gastos especialista: ${totalGastosCampo.toLocaleString('en-US', { minimumFractionDigits: 2 })} CUP
                    </p>
                  </div>
                  <div className="text-right bg-white border border-slate-300 rounded-lg px-4 py-2">
                    <p className="text-[10px] font-bold uppercase text-slate-600">Dinero Generado Total</p>
                    <p className="text-xl font-black text-sky-700 mt-0.5">
                      ${totalPresupuesto.toLocaleString('en-US', { minimumFractionDigits: 2 })} <span className="text-xs text-slate-500 font-normal">CUP</span>
                    </p>
                  </div>
                </div>
              </div>

              {/* Grid de 4 KPIs */}
              <div className="grid-4 grid grid-cols-4 gap-2.5 mb-6">
                <div className="card bg-white border border-slate-400 rounded p-2.5">
                  <span className="text-[10px] font-bold uppercase text-slate-600">Dinero Generado</span>
                  <div className="text-sm font-black text-sky-700 mt-1">${totalPresupuesto.toLocaleString('en-US', { minimumFractionDigits: 2 })}</div>
                  <span className="text-[9px] text-slate-500">Obras y proyectos</span>
                </div>
                <div className="card bg-white border border-slate-400 rounded p-2.5">
                  <span className="text-[10px] font-bold uppercase text-slate-600">Gastos Especialistas</span>
                  <div className="text-sm font-black text-amber-700 mt-1">${totalGastosCampo.toLocaleString('en-US', { minimumFractionDigits: 2 })}</div>
                  <span className="text-[9px] text-slate-500">Transp. ${totalTransporte.toLocaleString()} + Alm.</span>
                </div>
                <div className="card bg-white border border-slate-400 rounded p-2.5">
                  <span className="text-[10px] font-bold uppercase text-slate-600">Balance Neto</span>
                  <div className="text-sm font-black text-emerald-700 mt-1">${balanceNeto.toLocaleString('en-US', { minimumFractionDigits: 2 })}</div>
                  <span className="text-[9px] text-slate-500">Margen operativo</span>
                </div>
                <div className="card bg-white border border-slate-400 rounded p-2.5">
                  <span className="text-[10px] font-bold uppercase text-slate-600">Cumplimiento SLA</span>
                  <div className="text-sm font-black text-purple-700 mt-1">{tasaSla}%</div>
                  <span className="text-[9px] text-slate-500">{dentroSla} dentro / {fueraSla} fuera</span>
                </div>
              </div>

              {/* Tabla de Gastos por Especialista */}
              <div className="panel border border-slate-300 rounded mb-6 overflow-hidden">
                <div className="panel-hdr bg-slate-100 px-3 py-2 border-b border-slate-300 flex justify-between items-center">
                  <span className="font-bold text-slate-900 text-xs">1. Rendición de Gastos de Especialistas (Transporte y Almuerzo)</span>
                  <span className="font-bold text-amber-700 text-xs">Total: ${totalGastosCampo.toLocaleString('en-US', { minimumFractionDigits: 2 })} CUP</span>
                </div>
                <table className="w-full text-left text-[11px] border-collapse">
                  <thead>
                    <tr className="bg-slate-50 border-b border-slate-300 text-slate-700">
                      <th className="p-2 font-bold">Especialista / Técnico</th>
                      <th className="p-2 text-center font-bold">Asistencias</th>
                      <th className="p-2 text-right font-bold">Gasto Transporte</th>
                      <th className="p-2 text-right font-bold">Gasto Almuerzo</th>
                      <th className="p-2 text-right font-bold">Total Gastos Campo</th>
                    </tr>
                  </thead>
                  <tbody>
                    {gastosPorEspecialista.map((esp, i) => (
                      <tr key={i} className="border-b border-slate-200">
                        <td className="p-2 font-bold text-slate-900">{esp.nombre}</td>
                        <td className="p-2 text-center font-bold">{esp.total_incidencias}</td>
                        <td className="p-2 text-right font-mono">${esp.transporte.toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                        <td className="p-2 text-right font-mono">${esp.almuerzo.toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                        <td className="p-2 text-right font-mono font-bold text-slate-900">${esp.total.toLocaleString('en-US', { minimumFractionDigits: 2 })} CUP</td>
                      </tr>
                    ))}
                    {gastosPorEspecialista.length === 0 && (
                      <tr>
                        <td colSpan={5} className="p-4 text-center text-slate-500">No hay gastos de especialistas registrados en este período.</td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>

              {/* Tabla de Incidencias */}
              <div className="panel border border-slate-300 rounded mb-8 overflow-hidden">
                <div className="panel-hdr bg-slate-100 px-3 py-2 border-b border-slate-300 flex justify-between items-center">
                  <span className="font-bold text-slate-900 text-xs">2. Registro de Incidencias Técnicas & Cumplimiento SLA</span>
                  <span className="text-slate-600 text-xs">{incidenciasFiltradas.length} asistencias</span>
                </div>
                <table className="w-full text-left text-[11px] border-collapse">
                  <thead>
                    <tr className="bg-slate-50 border-b border-slate-300 text-slate-700">
                      <th className="p-2 font-bold">Código / Fecha</th>
                      <th className="p-2 font-bold">Cliente</th>
                      <th className="p-2 font-bold">Servicio</th>
                      <th className="p-2 font-bold">Especialista</th>
                      <th className="p-2 font-bold">Estado / SLA</th>
                      <th className="p-2 text-right font-bold">Transp.</th>
                      <th className="p-2 text-right font-bold">Alm.</th>
                      <th className="p-2 text-right font-bold">Total Gastos</th>
                    </tr>
                  </thead>
                  <tbody>
                    {incidenciasFiltradas.map((inc) => (
                      <tr key={inc.id} className="border-b border-slate-200">
                        <td className="p-2">
                          <span className="font-mono font-bold text-sky-700">{inc.codigo}</span>
                          <div className="text-[9px] text-slate-500">{inc.fecha_reporte}</div>
                        </td>
                        <td className="p-2 font-semibold text-slate-900">{inc.cliente_nombre}</td>
                        <td className="p-2 text-slate-700">{inc.tipo}</td>
                        <td className="p-2 text-slate-700">{inc.tecnico_nombre || 'Sin asignar'}</td>
                        <td className="p-2">
                          {inc.cumplio_sla === true ? (
                            <span className="text-emerald-700 font-bold">✓ En SLA</span>
                          ) : inc.cumplio_sla === false ? (
                            <span className="text-rose-700 font-bold">⚠ Fuera SLA</span>
                          ) : (
                            <span className="text-amber-700 font-bold">{inc.estado}</span>
                          )}
                        </td>
                        <td className="p-2 text-right font-mono">${(inc.gasto_transporte || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                        <td className="p-2 text-right font-mono">${(inc.gasto_almuerzo || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}</td>
                        <td className="p-2 text-right font-mono font-bold text-slate-900">
                          ${((inc.gasto_transporte || 0) + (inc.gasto_almuerzo || 0)).toLocaleString('en-US', { minimumFractionDigits: 2 })}
                        </td>
                      </tr>
                    ))}
                    {incidenciasFiltradas.length === 0 && (
                      <tr>
                        <td colSpan={8} className="p-4 text-center text-slate-500">No hay incidencias registradas en este período.</td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>

              {/* Firmas Institucionales */}
              <div className="signatures grid grid-cols-2 gap-12 mt-8 pt-4">
                <div className="sig-line border-t-2 border-slate-900 pt-2 text-center">
                  <p className="font-bold text-xs text-slate-900">Firma del Responsable de Operaciones</p>
                  <p className="text-[10px] text-slate-600 mt-0.5">DataPlus S.R.L.</p>
                </div>
                <div className="sig-line border-t-2 border-slate-900 pt-2 text-center">
                  <p className="font-bold text-xs text-slate-900">VºBº Dirección General / Auditoría</p>
                  <p className="text-[10px] text-slate-600 mt-0.5">DataPlus S.R.L.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
