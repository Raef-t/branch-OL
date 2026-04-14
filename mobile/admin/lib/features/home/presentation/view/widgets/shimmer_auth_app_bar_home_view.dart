import 'package:flutter/material.dart';
import 'package:second_page_app/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/components/shimmer_loading_widget.dart';
import '/core/helpers/get_scale_factor_helper.dart';

/// Skeleton loader for the auth user-info section in the home app bar.
/// Sizes are computed with the exact same formulas as the real widgets:
///   avatar  → (35 × scaleFactor).clamp(58, 78)   [CustomProfileMissImageHomeView]
///   padding → width × 0.049                       [OnlyPaddingWithChild.right20]
class ShimmerAuthAppBarHomeView extends StatelessWidget {
  const ShimmerAuthAppBarHomeView({super.key});

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.sizeOf(context);
    // Exact formula used by CustomProfileMissImageHomeView
    final avatarSize = (35 * getScaleFactorHelper(context: context)).clamp(
      58.0,
      78.0,
    );

    return ShimmerLoadingWidget(
      child: Container(
        height: size.height,
        padding: EdgeInsets.only(
          right: size.width * 0.049,
        ), // same height as the real app bar section
        // right20
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              mainAxisAlignment: MainAxisAlignment.center,
              mainAxisSize: MainAxisSize.min,
              children: [
                // Greeting + name line  (CustomImageAndTextInAppBarHomeView)
                ShimmerBox(
                  width: size.width * 0.28,
                  height: 14,
                  borderRadius: 7,
                ),
                const SizedBox(height: 4),

                ShimmerBox(
                  width: size.width * 0.38,
                  height: 13,
                  borderRadius: 6,
                ),
              ],
            ),
            SizedBox(width: size.width * 0.02), // width8
            // Avatar circle — same fixed clamped size
            ShimmerCircle(diameter: avatarSize),
          ],
        ),
      ),
    );
  }
}
