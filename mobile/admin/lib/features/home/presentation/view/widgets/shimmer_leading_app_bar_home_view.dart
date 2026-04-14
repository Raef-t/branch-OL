import 'package:flutter/material.dart';
import '/core/components/shimmer_loading_widget.dart';

/// Skeleton loader for the leading section of the home app bar.
/// Matches [CustomLeadingAppBarInHomeView]: left-padding → Row( logo, arrow ).
class ShimmerLeadingAppBarHomeView extends StatelessWidget {
  const ShimmerLeadingAppBarHomeView({super.key});

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.sizeOf(context);
    final logoH = size.height * 0.049;

    return ShimmerLoadingWidget(
      child: Padding(
        padding: EdgeInsets.only(left: size.width * 0.046), // left19
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Institute logo placeholder
            ShimmerBox(
              width: logoH * 2.6, // approximate logo aspect-ratio
              height: logoH,
              borderRadius: 6,
            ),
            SizedBox(width: size.width * 0.017), // width7
            // Bottom-arrow icon placeholder
            const ShimmerBox(width: 12, height: 12, borderRadius: 3),
          ],
        ),
      ),
    );
  }
}
