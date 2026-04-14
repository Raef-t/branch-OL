import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';
import '/core/styles/texts_style.dart';

class BatchNameTooltipWidget extends StatelessWidget {
  final String batchName;

  const BatchNameTooltipWidget({super.key, required this.batchName});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: CustomPaint(
        painter: _TooltipPainter(),
        child: Padding(
          padding: const EdgeInsets.fromLTRB(20, 22, 20, 14),
          child: ConstrainedBox(
            constraints: BoxConstraints(
              maxWidth: MediaQuery.sizeOf(context).width * 0.5,
            ),
            child: Text(
              batchName,
              textAlign: TextAlign.center,
              style: TextsStyle.bold14(context: context).copyWith(
                color: ColorsStyle.greyColor,
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class _TooltipPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = ColorsStyle.whiteColor
      ..style = PaintingStyle.fill;

    final shadowPaint = Paint()
      ..color = Colors.black.withValues(alpha: 0.12)
      ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 6);

    const arrowWidth = 18.0;
    const arrowHeight = 10.0;
    const radius = 14.0;
    const arrowCenterX = 0.5;

    final path = _buildPath(size, arrowWidth, arrowHeight, radius, arrowCenterX);

    canvas.drawPath(path, shadowPaint);
    canvas.drawPath(path, paint);
  }

  Path _buildPath(
    Size size,
    double arrowWidth,
    double arrowHeight,
    double radius,
    double arrowCenterX,
  ) {
    final cx = size.width * arrowCenterX;
    final path = Path();

    path.moveTo(cx - arrowWidth / 2, arrowHeight);
    path.lineTo(cx, 0);
    path.lineTo(cx + arrowWidth / 2, arrowHeight);

    path.lineTo(size.width - radius, arrowHeight);
    path.arcToPoint(
      Offset(size.width, arrowHeight + radius),
      radius: Radius.circular(radius),
    );
    path.lineTo(size.width, size.height - radius);
    path.arcToPoint(
      Offset(size.width - radius, size.height),
      radius: Radius.circular(radius),
    );
    path.lineTo(radius, size.height);
    path.arcToPoint(
      Offset(0, size.height - radius),
      radius: Radius.circular(radius),
    );
    path.lineTo(0, arrowHeight + radius);
    path.arcToPoint(
      Offset(radius, arrowHeight),
      radius: Radius.circular(radius),
    );
    path.close();

    return path;
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
