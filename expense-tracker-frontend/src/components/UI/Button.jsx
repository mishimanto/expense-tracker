export default function Button({
  children,
  className = "",
  ...props
}) {
  return (
    <button
      {...props}
      className={`
        w-full py-2 rounded-lg
        bg-blue-600 text-white font-medium
        hover:bg-blue-700
        transition duration-200
        ${className}
      `}
    >
      {children}
    </button>
  );
}
