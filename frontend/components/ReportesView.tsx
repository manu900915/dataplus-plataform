import React from 'react';
import { 
  FileText, 
  Download, 
  Printer, 
  TrendingUp, 
  DollarSign, 
  Boxes, 
  AlertCircle, 
  CheckCircle2, 
  Calendar,
  Layers
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
  const totalPresupuesto = proyectos.reduce((acc, p) => acc + p.presupuesto_total, 0);
  const totalItemsValuation = items.reduce((acc, i) => acc + (i.stock_actual * i.precio_unitario), 0);
  const incidenciasResueltas = incidencias.filter(i => i.estado === 'Resuelta' || i.estado === 'Cerrada').length;
  const tasaResolucion = incidencias.length > 0 ? Math.round((incidenciasResueltas / incidencias.length) * 100) : 100;

  const handleExportCSV = () => {
    const rows = [
      ['Modulo', 'Codigo', 'Nombre/Titulo', 'Estado', 'Monto/Valor'],
      ...proyectos.map(p => ['Proyecto', p.codigo, p.nombre, p.estado, p.presupuesto_total.toString()]),
      ...incidencias.map(i => ['Incidencia', i.codigo, i.titulo, i.estado, (i.costo_estimado || 0).toString()]),
      ...items.map(it => ['Inventario', it.codigo, it.nombre, `Stock: ${it.stock_actual}`, (it.stock_actual * it.precio_unitario).toString()])
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

  const handlePrint = () => {
    window.print();
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h2 className="text-xl font-bold text-white tracking-tight flex items-center gap-2">
            <FileText className="h-5 w-5 text-teal-400" />
            Informes & Métricas Operativas Globales
          </h2>
          <p className="text-xs text-slate-400">
            Consolidado ejecutivo de finanzas, rendimiento de obras y disponibilidad de inventario.
          </p>
        </div>

        <div className="flex items-center gap-2">
          <button
            onClick={handlePrint}
            className="flex items-center gap-1.5 rounded-lg border border-slate-700 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold px-3 py-2 text-xs transition-colors cursor-pointer"
          >
            <Printer className="h-3.5 w-3.5" />
            Imprimir Reporte
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

      {/* Highlights Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
          <div className="flex items-center justify-between text-slate-400">
            <span className="text-xs font-semibold uppercase tracking-wider">Cartera Presupuestaria</span>
            <div className="rounded-lg bg-emerald-500/10 p-2 text-emerald-400 border border-emerald-500/20">
              <DollarSign className="h-4 w-4" />
            </div>
          </div>
          <div className="mt-3 text-2xl font-bold text-white font-mono">
            ${totalPresupuesto.toLocaleString('en-US', { minimumFractionDigits: 2 })}
          </div>
          <div className="mt-1 text-xs text-slate-400">
            Total contratado en {proyectos.length} proyectos
          </div>
        </div>

        <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
          <div className="flex items-center justify-between text-slate-400">
            <span className="text-xs font-semibold uppercase tracking-wider">Valoración de Activos</span>
            <div className="rounded-lg bg-teal-500/10 p-2 text-teal-400 border border-teal-500/20">
              <Boxes className="h-4 w-4" />
            </div>
          </div>
          <div className="mt-3 text-2xl font-bold text-white font-mono">
            ${totalItemsValuation.toLocaleString('en-US', { minimumFractionDigits: 2 })}
          </div>
          <div className="mt-1 text-xs text-slate-400">
            En {almacenes.length} almacenes territoriales
          </div>
        </div>

        <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5">
          <div className="flex items-center justify-between text-slate-400">
            <span className="text-xs font-semibold uppercase tracking-wider">Tasa de Resolución</span>
            <div className="rounded-lg bg-sky-500/10 p-2 text-sky-400 border border-sky-500/20">
              <CheckCircle2 className="h-4 w-4" />
            </div>
          </div>
          <div className="mt-3 text-2xl font-bold text-white font-mono">
            {tasaResolucion}%
          </div>
          <div className="mt-1 text-xs text-slate-400">
            {incidenciasResueltas} de {incidencias.length} incidencias resueltas
          </div>
        </div>
      </div>

      {/* Summary Table: Warehouse Inventory Breakdown */}
      <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-5 shadow-sm">
        <h3 className="text-sm font-bold text-white mb-3 flex items-center gap-2">
          <Boxes className="h-4 w-4 text-teal-400" />
          Valoración y Stock por Centro de Almacenamiento
        </h3>

        <div className="overflow-x-auto">
          <table className="w-full text-left text-xs">
            <thead className="border-b border-slate-800 bg-slate-950/80 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
              <tr>
                <th className="px-4 py-3">Almacén</th>
                <th className="px-4 py-3">Ubicación</th>
                <th className="px-4 py-3">Responsable</th>
                <th className="px-4 py-3 text-right">Líneas de Ítems</th>
                <th className="px-4 py-3 text-right">Unidades Totales</th>
                <th className="px-4 py-3 text-right">Valoración Estimada</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-800 text-slate-200">
              {almacenes.map((alm) => {
                const almItems = items.filter(i => i.almacen_id === alm.id);
                const count = almItems.reduce((acc, i) => acc + i.stock_actual, 0);
                const val = almItems.reduce((acc, i) => acc + (i.stock_actual * i.precio_unitario), 0);

                return (
                  <tr key={alm.id} className="hover:bg-slate-800/40">
                    <td className="px-4 py-3 font-semibold text-white">{alm.nombre}</td>
                    <td className="px-4 py-3 text-slate-300">{alm.municipio ? `${alm.municipio}, ` : ''}{alm.provincia}</td>
                    <td className="px-4 py-3 text-slate-400">{alm.responsable || 'Custodio general'}</td>
                    <td className="px-4 py-3 text-right font-mono">{almItems.length}</td>
                    <td className="px-4 py-3 text-right font-mono font-bold text-slate-200">{count} u</td>
                    <td className="px-4 py-3 text-right font-mono font-bold text-teal-400">
                      ${val.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};
