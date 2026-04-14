import 'package:flutter/material.dart';
import '/core/components/text_medium10_component.dart';
import '/core/sized_boxs/heights.dart';
import '/features/home/presentation/view/widgets/custom_circle_with_check_image_in_home_view.dart';

class CustomCircleWithCheckAndTextInHomeView extends StatelessWidget {
  const CustomCircleWithCheckAndTextInHomeView({
    super.key,
    required this.name,
    this.onTap,
  });
  final String name;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    return GestureDetector(
      onTap: onTap,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          const CustomCircleWithCheckImageInHomeView(),
          Heights.height6(context: context),
          SizedBox(
            width: size.width * 0.14,
            child: TextMedium10Component(
              text: name,
              color: Colors.black,
              textAlign: TextAlign.center,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ),
        ],
      ),
    );
  }
}
