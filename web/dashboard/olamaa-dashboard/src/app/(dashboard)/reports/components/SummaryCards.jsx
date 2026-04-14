"use client";

import { Users, CalendarCheck, CalendarX, Clock } from "lucide-react";

/**
 * SummaryCards component
 * Displays a grid of summary metric cards.
 * @param {Array} cards - Objects containing label, value, and icon type.
 */
export default function SummaryCards({ cards = [] }) {
  const getIcon = (type) => {
    switch (type) {
      case "employees":
        return <Users className="text-blue-500" size={24} />;
      case "attendance":
        return <CalendarCheck className="text-green-500" size={24} />;
      case "absence":
        return <CalendarX className="text-red-500" size={24} />;
      case "delays":
        return <Clock className="text-orange-500" size={24} />;
      default:
        return <Users className="text-gray-500" size={24} />;
    }
  };

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      {cards.map((card, idx) => (
        <div
          key={idx}
          className="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4 transition hover:shadow-md"
        >
          <div className="p-3 bg-gray-50 rounded-xl">{getIcon(card.iconType)}</div>
          <div>
            <p className="text-sm text-gray-500 font-medium">{card.label}</p>
            <p className="text-2xl font-bold text-gray-800">{card.value}</p>
          </div>
        </div>
      ))}
    </div>
  );
}
