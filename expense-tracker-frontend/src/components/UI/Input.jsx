export default function Input({
  label,
  type = "text",
  className = "",
  ...props
}) {
  return (
    <div className="w-full">
      {label && (
        <label className="block text-sm font-medium text-gray-300 mb-1">
          {label}
        </label>
      )}

      <input
        type={type}
        {...props}
        className={`
          w-full px-4 py-2
          rounded-lg
          bg-gray-900 text-white
          border border-gray-700
          placeholder-gray-400
          focus:outline-none focus:ring-2 focus:ring-blue-500
          ${className}
        `}
      />
    </div>
  );
}
